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
    $limit = $request->query('limit', 10);

    $releases = Release::select('id', 'title', 'description', 'images', 'file_path', 'excel_path', 'powerbi_path', 'created_at')
        ->orderBy('created_at', 'desc')
        ->paginate($limit);

    $releases->getCollection()->transform(function ($release) {
        $images = is_string($release->images)
            ? json_decode($release->images, true)
            : $release->images;

        return [
            'id' => $release->id,
            'title' => $release->title,
            'description' => $release->description,
            'images' => collect($images)->map(fn($img) => asset($img))->toArray(),
            'file_url' => $release->file_path ? asset($release->file_path) : null,
            'excel_url' => $release->excel_path ? asset($release->excel_path) : null,
            'powerbi_url' => $release->powerbi_path ? asset($release->powerbi_path) : null,
            'created_at' => $release->created_at,
        ];
    });

    return response()->json($releases);
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
