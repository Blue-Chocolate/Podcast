<?php

namespace App\Repositories;

use App\Models\DocVideo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class DocVideoRepository
{
    public function all($limit = 10, $offset = 0)
    {
        return DocVideo::offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function count()
    {
        return DocVideo::count();
    }

    public function find($id)
    {
        $doc_video = DocVideo::find($id);
        if (!$doc_video) {
            throw new ModelNotFoundException('Doc video not found.');
        }
        return $doc_video;
    }

    public function create(array $data)
    {
        try {
            return DocVideo::create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create doc video: ' . $e->getMessage());
        }
    }

    public function update(DocVideo $doc_video, array $data)
    {
        try {
            $doc_video->update($data);
            return $doc_video;
        } catch (Exception $e) {
            throw new Exception('Failed to update doc video: ' . $e->getMessage());
        }
    }

    public function delete(DocVideo $doc_video)
    {
        try {
            return $doc_video->delete();
        } catch (Exception $e) {
            throw new Exception('Failed to delete doc video: ' . $e->getMessage());
        }
    }
}
