<?php

namespace App\Http\Controllers\Api\ReleaseController;

use App\Http\Controllers\Controller;
use App\Models\Release;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReleaseController extends Controller
{     

  
    /**
     * Get all releases (public)
     */
    public function show($id)
{
    $release = Release::select('id', 'title', 'description', 'images', 'file_path', 'excel_path', 'powerbi_path', 'created_at')
        ->find($id);

    if (!$release) {
        return response()->json(['error' => 'Release not found.'], 404);
    }

    // Generate full image URLs directly
    $images = collect($release->images ?? [])
        ->map(fn($img) => asset('storage/' . $img))
        ->toArray();

    return response()->json([
        'id' => $release->id,
        'title' => $release->title,
        'description' => $release->description,
        'images' => $images,
        'file_url' => $release->file_path ? asset('storage/' . $release->file_path) : null,
        'excel_url' => $release->excel_path ? asset('storage/' . $release->excel_path) : null,
        'powerbi_url' => $release->powerbi_path ? asset('storage/' . $release->powerbi_path) : null,
        'created_at' => $release->created_at,
    ]);
}
  public function index(Request $request)
{
    try {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);

        // Validate pagination
        if ($limit < 1 || $limit > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Limit must be between 1 and 100',
            ], 400);
        }

        if ($page < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Page must be greater than 0',
            ], 400);
        }

        // Fetch releases
        $releases = Release::select('id', 'title', 'description', 'images', 'file_path', 'views_count', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        // Transform releases into desired structure
        $formatted = $releases->getCollection()->transform(function ($release) {
            // Decode images (in case stored as JSON)
            $images = is_string($release->images)
                ? json_decode($release->images, true)
                : $release->images;

            // Pick the first image if exists
            $mainImage = !empty($images)
                ? asset(ltrim($images[0], '/'))
                : null;

            return [
                'id' => $release->id,
                'title' => $release->title,
                'short_description' => Str::limit($release->description, 120),
                'image' => $mainImage,
                'views' => $release->views_count ?? 0,
                'published_at' => $release->created_at ? $release->created_at->timestamp : null,
                'pdf_url' => $release->file_path ? asset($release->file_path) : null,
            ];
        });

        // Response
        return response()->json([
            'success' => true,
            'data' => $formatted,
            'pagination' => [
                'current_page' => $releases->currentPage(),
                'per_page' => $releases->perPage(),
                'total_items' => $releases->total(),
                'last_page' => $releases->lastPage(),
            ],
        ]);

    } catch (Exception $e) {
        Log::error('Error fetching releases list', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching releases',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}


    /**
     * Store a new release (admin only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:20480',
            'excel' => 'nullable|file|mimes:xlsx,xls|max:20480',
            'powerbi' => 'nullable|file|mimes:pbix|max:51200',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $pdfPath = $request->hasFile('file')
            ? $request->file('file')->store('releases', 'public')
            : null;

        $excelPath = $request->hasFile('excel')
            ? $request->file('excel')->store('releases', 'public')
            : null;

        $powerbiPath = $request->hasFile('powerbi')
            ? $request->file('powerbi')->store('releases', 'public')
            : null;

        // Handle multiple image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('releases/images', 'public');
            }
        }

        $release = Release::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $pdfPath,
            'excel_path' => $excelPath,
            'powerbi_path' => $powerbiPath,
            'images' => $imagePaths ? json_encode($imagePaths) : null,
        ]);

        return response()->json([
            'message' => 'Release created successfully',
            'data' => $release
        ]);
    }

    /**
     * Download file (requires authentication)
     */
    public function download($id, $type = 'pdf')
{
    if (!Auth::guard('sanctum')->check() && !Auth::check()) {
        return response()->json([
            'error' => 'Unauthorized. Please login first.',
            'redirect' => '/login'
        ], 401)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }

    $release = Release::find($id);
    if (!$release) {
        return response()->json(['error' => 'Release not found.'], 404)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }

    $path = match ($type) {
        'pdf' => $release->file_path,
        'excel' => $release->excel_path,
        'powerbi' => $release->powerbi_path,
        default => null,
    };

    if (!$path) {
        return response()->json(['error' => 'File type not found.'], 404)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }

    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        return response()->json(['error' => 'File not found.'], 404)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }

    $fileName = $release->title . '.' . match ($type) {
        'pdf' => 'pdf',
        'excel' => 'xlsx',
        'powerbi' => 'pbix',
    };

    return response()->download($fullPath, $fileName)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
}

}
