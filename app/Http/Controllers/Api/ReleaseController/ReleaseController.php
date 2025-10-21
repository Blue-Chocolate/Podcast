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
    public function index()
    {
        $releases = Release::select('id', 'title', 'description', 'images', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($release) {
                // Decode images JSON and prepend full URLs
                $release->images = $release->images ? collect(json_decode($release->images))
                    ->map(fn($img) => asset('storage/' . $img))
                    ->toArray() : [];
                return $release;
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
            ], 401);
        }

        $release = Release::find($id);
        if (!$release) {
            return response()->json(['error' => 'Release not found.'], 404);
        }

        $path = match ($type) {
            'pdf' => $release->file_path,
            'excel' => $release->excel_path,
            'powerbi' => $release->powerbi_path,
            default => null,
        };

        if (!$path) {
            return response()->json(['error' => 'File type not found.'], 404);
        }

        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        $fileName = $release->title . '.' . match ($type) {
            'pdf' => 'pdf',
            'excel' => 'xlsx',
            'powerbi' => 'pbix',
        };

        return response()->download($fullPath, $fileName);
    }
}
