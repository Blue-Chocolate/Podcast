<?php

namespace App\Repositories;

use App\Models\Attachment;
use Illuminate\Support\Str;

class AttachmentRepository
{
    public function storeAttachments($req, int $submissionId): void
    {
        $files = $req->file('attachments');
        $axes = $req->input('attachments_axes', []);

        foreach ($files as $idx => $file) {
            if (!$file->isValid()) continue;

            $original = $file->getClientOriginalName();
            $path = $file->storeAs('submissions/'.$submissionId, Str::random(12).'_'.$original, 'public');

            Attachment::create([
                'submission_id' => $submissionId,
                'axis' => $axes[$idx] ?? null,
                'original_name' => $original,
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }
}
