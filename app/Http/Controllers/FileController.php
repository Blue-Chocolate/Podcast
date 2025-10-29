<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function serve($filename)
    {
        $path = storage_path("app/public/{$filename}");

        if (!file_exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->file($path, [
            'Access-Control-Allow-Origin' => '*',    // Allow any frontend
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        ]);
    }
}
