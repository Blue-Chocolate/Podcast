<?php

namespace App\Http\Controllers\Api\TranscriptController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Actions\Transcripts\{
    CreateTranscriptAction,
    UpdateTranscriptAction,
    DeleteTranscriptAction,
    ShowTranscriptAction,
    ListTranscriptsAction
};
use App\Models\Transcript;

class TranscriptController extends Controller
{
    public function index(ListTranscriptsAction $action)
    {
        try {
            $data = $action->execute();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch transcripts'], 500);
        }
    }

    public function show($id, ShowTranscriptAction $action)
    {
        try {
            $transcript = $action->execute($id);
            return response()->json($transcript);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Transcript not found'], 404);
        }
    }

    public function store(Request $request, CreateTranscriptAction $action)
    {
        $data = $request->validate([
            'episode_id' => 'required|exists:episodes,id',
            'transcript_text' => 'nullable|string',
            'language' => 'nullable|string|max:10',
            'transcript_file' => 'nullable|file|mimes:txt,pdf,vtt,srt|max:2048',
        ]);

        if ($request->hasFile('transcript_file')) {
            $path = $request->file('transcript_file')->store('transcripts', 'public');
            $data['transcript_file_url'] = Storage::url($path);
        }

        try {
            $transcript = $action->execute($data);
            return response()->json($transcript, 201);
        } catch (\Exception $e) {
            Log::error('Transcript store error: '.$e->getMessage());
            return response()->json(['error' => 'Failed to create transcript'], 500);
        }
    }

    public function update(Request $request, Transcript $transcript, UpdateTranscriptAction $action)
    {
        $data = $request->validate([
            'transcript_text' => 'nullable|string',
            'language' => 'nullable|string|max:10',
            'transcript_file' => 'nullable|file|mimes:txt,pdf,vtt,srt|max:2048',
        ]);

        try {
            if ($request->hasFile('transcript_file')) {
                if ($transcript->transcript_file_url) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $transcript->transcript_file_url));
                }
                $path = $request->file('transcript_file')->store('transcripts', 'public');
                $data['transcript_file_url'] = Storage::url($path);
            }

            $updated = $action->execute($transcript, $data);
            return response()->json($updated);
        } catch (\Exception $e) {
            Log::error('Transcript update error: '.$e->getMessage());
            return response()->json(['error' => 'Failed to update transcript'], 500);
        }
    }

    public function destroy(Transcript $transcript, DeleteTranscriptAction $action)
    {
        try {
            $action->execute($transcript);
            return response()->json(['message' => 'Transcript deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Transcript delete error: '.$e->getMessage());
            return response()->json(['error' => 'Failed to delete transcript'], 500);
        }
    }
}
