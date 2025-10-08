<?php

namespace App\Repositories;

use App\Models\Transcript;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class TranscriptRepository
{
    public function getAll()
    {
        return Transcript::with('episode')->latest()->get();
    }

    public function find($id)
    {
        return Transcript::with('episode')->findOrFail($id);
    }

    public function create(array $data)
    {
        try {
            return Transcript::create($data);
        } catch (Exception $e) {
            Log::error('Transcript creation failed: '.$e->getMessage());
            throw new \RuntimeException('Failed to create transcript');
        }
    }

    public function update(Transcript $transcript, array $data)
    {
        try {
            $transcript->update($data);
            return $transcript;
        } catch (Exception $e) {
            Log::error('Transcript update failed: '.$e->getMessage());
            throw new \RuntimeException('Failed to update transcript');
        }
    }

    public function delete(Transcript $transcript)
    {
        try {
            if ($transcript->transcript_file_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $transcript->transcript_file_url));
            }
            return $transcript->delete();
        } catch (Exception $e) {
            Log::error('Transcript deletion failed: '.$e->getMessage());
            throw new \RuntimeException('Failed to delete transcript');
        }
    }
}
