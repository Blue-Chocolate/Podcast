<?php

namespace App\Http\Controllers\Api\EpisodeFileController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\EpisodeFile\{
    CreateEpisodeFileAction,
    EditEpisodeFileAction,
    UpdateEpisodeFileAction,
    DeleteEpisodeFileAction,
    IndexEpisodeFilesAction
};

class EpisodeFileController extends Controller
{


     protected $indexAction;

    public function __construct(IndexEpisodeFilesAction $indexAction)
    {
        $this->indexAction = $indexAction;
    }

    public function index($episodeId)
    {
        $files = $this->indexAction->execute($episodeId);
        return response()->json($files);
    }

    public function show($id, \App\Actions\EpisodeFile\ShowEpisodeFileAction $action)
{
    $file = $action->execute($id);
    return response()->json($file);
}

    public function store(Request $request, CreateEpisodeFileAction $action)
    {
        $data = $request->validate([
            'episode_id' => 'required|exists:episodes,id',
            'file_url' => 'required|string|max:1000',
            'mime_type' => 'nullable|string|max:100',
            'file_size_bytes' => 'nullable|integer',
            'bitrate_kbps' => 'nullable|integer',
            'format' => 'nullable|string|max:50',
        ]);

        $episodeFile = $action->execute($data);
        return response()->json($episodeFile, 201);
    }

    public function edit($id, EditEpisodeFileAction $action)
    {
        $episodeFile = $action->execute($id);
        return response()->json($episodeFile);
    }

    public function update(Request $request, $id, UpdateEpisodeFileAction $action)
    {
        $data = $request->validate([
            'file_url' => 'sometimes|string|max:1000',
            'mime_type' => 'nullable|string|max:100',
            'file_size_bytes' => 'nullable|integer',
            'bitrate_kbps' => 'nullable|integer',
            'format' => 'nullable|string|max:50',
        ]);

        $episodeFile = $action->execute($id, $data);
        return response()->json($episodeFile);
    }

    public function destroy($id, DeleteEpisodeFileAction $action)
    {
        $action->execute($id);
        return response()->json(['message' => 'Episode file deleted successfully']);
    }
}
