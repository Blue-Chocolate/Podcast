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
        $releases = Release::select('id', 'title', 'description', 'image', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($releases);
    }

    /**
     * Download release file (requires authentication)
     */
    public function download($id)
    {
        // Check if user is authenticated (supports both Sanctum token and session)
        if (!Auth::guard('sanctum')->check() && !Auth::check()) {
            return response()->json([
                'error' => 'Unauthorized. Please login first.',
                'redirect' => '/login'
            ], 401);
        }

        // Find the release
        $release = Release::find($id);
        
        if (!$release) {
            return response()->json(['error' => 'Release not found.'], 404);
        }

        // Check if file exists
        $path = storage_path('app/public/' . $release->file_path);

        if (!file_exists($path)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        // Return the file for download
        return response()->download($path, $release->title . '.pdf');
    }
}