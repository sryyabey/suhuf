<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class QuranDatabaseController extends Controller
{
    public function download(Request $request): BinaryFileResponse|JsonResponse
    {
        $candidates = [
            'quran.dv.zip',
            'quran.db.zip',
        ];

        foreach ($candidates as $filename) {
            $path = storage_path('data/'.$filename);

            if (is_file($path)) {
                return response()->download($path, $filename, [
                    'Content-Type' => 'application/zip',
                    'Cache-Control' => 'private, max-age=3600',
                ]);
            }
        }

        return response()->json([
            'message' => 'Quran database file not found.',
        ], 404);
    }
}
