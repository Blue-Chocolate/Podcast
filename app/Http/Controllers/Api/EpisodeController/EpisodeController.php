<?php

namespace App\Http\Controllers\Api\EpisodeController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Episode;
use Illuminate\Validation\ValidationException;
use App\Actions\Episodes\{
CreateEpisodeAction,
UpdateEpisodeAction,
DeleteEpisodeAction,
ShowEpisodeAction,
ListEpisodesAction
};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class EpisodeController extends Controller
{
public function index(ListEpisodesAction $action, Request $request)
{
    try {
        $limit = $request->query('limit', 10);

        $episodes = \App\Models\Episode::with('podcast:id,title')
            ->select('id', 'podcast_id', 'title', 'description', 'audio_url', 'cover_image', 'video_url', 'created_at')
            ->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $episodes->map(function ($episode) {
                return [
                    'id' => $episode->id,
                    'title' => $episode->title,
                    'description' => $episode->description,
                    'audio_url' => $episode->audio_url,
                    'video_url' => $episode->video_url,
                    'created_at' => $episode->created_at,
                    'podcast_id' => $episode->podcast_id,
                    'podcast_title' => $episode->podcast->title ?? null,
                    'cover_image' => $episode->cover_image,

                ];
            })
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

public function show($id, ShowEpisodeAction $action)
{
try {
// ✅ Fetch episode by ID
$episode = Episode::findOrFail($id);

// ✅ Increment views safely
$episode->increment('views_count');

// ✅ Ensure URLs are full paths
$episode->video_url = $episode->video_filename 
? url('storage/videos/' . $episode->video_filename) 
: null;

$episode->audio_url = $episode->audio_filename 
? url('storage/audios/' . $episode->audio_filename) 
: null;

// ✅ Return JSON for API requests
if (request()->wantsJson()) {
return response()->json([
'status' => 'success',
'data' => $episode->fresh(),
]);
}

return view('episodes.show', compact('episode'));

} catch (ModelNotFoundException $e) {
if (request()->wantsJson()) {
return response()->json(['status' => 'error', 'message' => 'Episode not found'], 404);
}
abort(404, 'Episode not found');
} catch (Exception $e) {
if (request()->wantsJson()) {
return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
}
return redirect()->back()->withErrors(['error' => $e->getMessage()]);
}
}


public function store(Request $request, CreateEpisodeAction $action)
{
try {
$data = $request->validate([
'podcast_id' => 'required|exists:podcasts,id',
'season_id' => 'nullable|exists:seasons,id',
'title' => 'required|string|max:255',
'slug' => 'nullable|string|unique:episodes,slug',
'description' => 'nullable|string',
'duration' => 'nullable|integer',
'published_at' => 'nullable|date',
'video' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:51200',
'audio' => 'nullable|file|mimes:mp3,wav,aac|max:51200',
'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
]);

$data['slug'] = $data['slug'] ?? Str::slug($data['title']) . '_' . uniqid();

// ✅ Handle video upload
if ($request->hasFile('video')) {
$file = $request->file('video');
$fileName = time() . '_' . $file->getClientOriginalName();
$file->move(public_path('storage/videos'), $fileName);
$data['video_filename'] = $fileName;
$data['video_url'] = url('storage/videos/' . $fileName);
}

// ✅ Handle audio upload
if ($request->hasFile('audio')) {
$file = $request->file('audio');
$fileName = time() . '_' . $file->getClientOriginalName();
$file->move(public_path('storage/audios'), $fileName);
$data['audio_filename'] = $fileName;
$data['audio_url'] = url('storage/audios/' . $fileName);
}

// ✅ Handle image upload
if ($request->hasFile('image')) {
$file = $request->file('image');
$fileName = time() . '_' . $file->getClientOriginalName();
$file->move(public_path('storage/covers'), $fileName);
$data['image_url'] = url('covers/' . $fileName);
$data['image_url'] = url('images/' . $fileName);
}

$episode = $action->execute($data);

return response()->json(['status' => 'success', 'data' => $episode], 201);

} catch (ValidationException $e) {
return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
} catch (Exception $e) {
return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
}
}

public function update(Request $request, Episode $episode, UpdateEpisodeAction $action)
{
try {
$data = $request->validate([
'podcast_id' => 'sometimes|exists:podcasts,id',
'season_id' => 'nullable|exists:seasons,id',
'episode_number' => 'nullable|integer',
'title' => 'sometimes|string|max:255',
'slug' => 'nullable|string|max:200|unique:episodes,slug,' . $episode->id,
'description' => 'nullable|string',
'short_description' => 'nullable|string|max:500',
'duration_seconds' => 'nullable|integer',
'published_at' => 'nullable|date',
'explicit' => 'boolean',
'status' => 'in:draft,published,archived',
'cover_image' => 'nullable|string|max:500',
'categories' => 'nullable|array',
'categories.*' => 'exists:categories,id',
'file' => 'nullable|file|mimes:mp3,mp4,m4a,wav|max:51200',
]);

// ✅ Handle file upload
if ($request->hasFile('file')) {
$path = $request->file('file')->store('episodes', 'public');
$data['video_url'] = asset('storage/' . $path);
$data['mime_type'] = $request->file('file')->getMimeType();
$data['file_size'] = $request->file('file')->getSize();
}

$episode = $action->execute($episode, $data);
return response()->json(['status' => 'success', 'data' => $episode]);

} catch (ValidationException $e) {
return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
} catch (Exception $e) {
return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
}
}

public function destroy(Episode $episode, DeleteEpisodeAction $action)
{
try {
$action->execute($episode);
return response()->json(['status' => 'success', 'message' => 'Episode deleted successfully']);
} catch (Exception $e) {
return response()->json(['status' => 'error', 'message' => 'Failed to delete episode: ' . $e->getMessage()], 500);
}
}
}
