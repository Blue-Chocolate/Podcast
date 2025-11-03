<?php

namespace App\Http\Controllers\Api\ReleaseController;

use App\Http\Controllers\Controller;
use App\Models\Release;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class ReleaseController extends Controller
{     
    /**
     * Get single release details (PUBLIC - no auth required)
     */
    public function show($id)
    {
        try {
            $release = Release::select('id', 'title', 'description', 'images', 'created_at', 'views_count', 'file_path', 'excel_path', 'powerbi_path')
                ->find($id);

            if (!$release) {
                return response()->json([
                    'success' => false,
                    'error' => 'Release not found.'
                ], 404);
            }

            // Generate full image URLs
            $images = collect($release->images ?? [])
                ->map(fn($img) => asset('storage/' . $img))
                ->toArray();

            // Check if user is authenticated (but don't require it)
            $isAuthenticated = false;
            try {
                $isAuthenticated = Auth::guard('sanctum')->check() || Auth::check();
            } catch (Exception $e) {
                // If auth check fails, just continue as unauthenticated
                Log::info('Auth check failed in show method', ['error' => $e->getMessage()]);
            }

            $response = [
                'success' => true,
                'id' => $release->id,
                'title' => $release->title,
                'description' => $release->description,
                'images' => $images,
                'views' => $release->views_count ?? 0,
                'created_at' => $release->created_at,
                'has_pdf' => !empty($release->file_path),
                'has_excel' => !empty($release->excel_path),
                'has_powerbi' => !empty($release->powerbi_path),
                'is_authenticated' => $isAuthenticated,
            ];

            // Show download URLs only if authenticated
            if ($isAuthenticated) {
                $response['download_urls'] = [
                    'pdf' => !empty($release->file_path) ? route('releases.download', ['id' => $id, 'type' => 'pdf']) : null,
                    'excel' => !empty($release->excel_path) ? route('releases.download', ['id' => $id, 'type' => 'excel']) : null,
                    'powerbi' => !empty($release->powerbi_path) ? route('releases.download', ['id' => $id, 'type' => 'powerbi']) : null,
                ];
            } else {
                $response['message'] = 'Login to download files';
            }

            return response()->json($response);

        } catch (Exception $e) {
            Log::error('Error fetching release details', [
                'release_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching release details',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get all releases (PUBLIC - no auth required)
     */
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

            // Fetch releases (without file paths)
            $releases = Release::select('id', 'title', 'description', 'images', 'views_count', 'created_at')
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            // Check if user is authenticated (but don't require it)
            $isAuthenticated = false;
            try {
                $isAuthenticated = Auth::guard('sanctum')->check() || Auth::check();
            } catch (Exception $e) {
                // If auth check fails, just continue as unauthenticated
                Log::info('Auth check failed in index method', ['error' => $e->getMessage()]);
            }

            // Transform releases
            $formatted = $releases->getCollection()->transform(function ($release) use ($isAuthenticated) {
                // Decode images
                $images = is_string($release->images)
                    ? json_decode($release->images, true)
                    : $release->images;

                // Pick first image
                $mainImage = !empty($images)
                    ? asset('storage/' . ltrim($images[0], '/'))
                    : null;

                $data = [
                    'id' => $release->id,
                    'title' => $release->title,
                    'short_description' => Str::limit($release->description, 120),
                    'image' => $mainImage,
                    'views' => $release->views_count ?? 0,
                    'published_at' => $release->created_at ? $release->created_at->timestamp : null,
                ];

                // Add download URL only if authenticated
                if ($isAuthenticated) {
                    $data['download_url'] = route('releases.download', ['id' => $release->id, 'type' => 'pdf']);
                }

                return $data;
            });

            return response()->json([
                'success' => true,
                'data' => $formatted,
                'is_authenticated' => $isAuthenticated,
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
     * Store a new release (admin only - requires auth)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf|max:20480',
                'excel' => 'nullable|file|mimes:xlsx,xls|max:20480',
                'powerbi' => 'nullable|file|mimes:pbix|max:51200',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            ]);

            $pdfPath = null;
            $excelPath = null;
            $powerbiPath = null;

            // Handle PDF upload
            if ($request->hasFile('file')) {
                try {
                    $pdfPath = $request->file('file')->store('releases/files', 'public');
                } catch (Exception $e) {
                    Log::error('Error uploading PDF file', ['error' => $e->getMessage()]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload PDF file',
                    ], 500);
                }
            }

            // Handle Excel upload
            if ($request->hasFile('excel')) {
                try {
                    $excelPath = $request->file('excel')->store('releases/files', 'public');
                } catch (Exception $e) {
                    Log::error('Error uploading Excel file', ['error' => $e->getMessage()]);
                    // Cleanup PDF if it was uploaded
                    if ($pdfPath) Storage::disk('public')->delete($pdfPath);
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload Excel file',
                    ], 500);
                }
            }

            // Handle PowerBI upload
            if ($request->hasFile('powerbi')) {
                try {
                    $powerbiPath = $request->file('powerbi')->store('releases/files', 'public');
                } catch (Exception $e) {
                    Log::error('Error uploading PowerBI file', ['error' => $e->getMessage()]);
                    // Cleanup previously uploaded files
                    if ($pdfPath) Storage::disk('public')->delete($pdfPath);
                    if ($excelPath) Storage::disk('public')->delete($excelPath);
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload PowerBI file',
                    ], 500);
                }
            }

            // Handle multiple image uploads
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    try {
                        $imagePaths[] = $image->store('releases/images', 'public');
                    } catch (Exception $e) {
                        Log::error('Error uploading image', ['error' => $e->getMessage()]);
                        // Continue with other images
                    }
                }
            }

            $release = Release::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $pdfPath,
                'excel_path' => $excelPath,
                'powerbi_path' => $powerbiPath,
                'images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Release created successfully',
                'data' => $release
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Error creating release', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the release',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Download file (REQUIRES authentication)
     * This is the ONLY way to access files
     */
    public function download($id, $type = 'pdf')
    {
        try {
            // CRITICAL: Check authentication FIRST
            if (!Auth::guard('sanctum')->check() && !Auth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized. Please login to download files.',
                    'redirect' => '/login'
                ], 401);
            }

            // Find release
            $release = Release::find($id);
            if (!$release) {
                return response()->json([
                    'success' => false,
                    'error' => 'Release not found.'
                ], 404);
            }

            // Get file path based on type
            $path = match ($type) {
                'pdf' => $release->file_path,
                'excel' => $release->excel_path,
                'powerbi' => $release->powerbi_path,
                default => null,
            };

            if (!$path) {
                return response()->json([
                    'success' => false,
                    'error' => 'File type not available for this release.'
                ], 404);
            }

            // Check if file exists
            if (!Storage::disk('public')->exists($path)) {
                Log::error('File not found on disk', [
                    'release_id' => $id,
                    'type' => $type,
                    'path' => $path
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'File not found on server.'
                ], 404);
            }

            $fullPath = Storage::disk('public')->path($path);

            // Generate filename
            $extension = match ($type) {
                'pdf' => 'pdf',
                'excel' => 'xlsx',
                'powerbi' => 'pbix',
                default => 'file',
            };
            
            $fileName = Str::slug($release->title) . '.' . $extension;

            // Increment download/view count (optional)
            $release->increment('views_count');

            // Return file download
            return response()->download($fullPath, $fileName, [
                'Content-Type' => match ($type) {
                    'pdf' => 'application/pdf',
                    'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'powerbi' => 'application/octet-stream',
                    default => 'application/octet-stream',
                }
            ]);

        } catch (Exception $e) {
            Log::error('Error downloading file', [
                'release_id' => $id,
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while downloading the file',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}