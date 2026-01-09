<?php

namespace App\Http\Controllers;

use App\Services\Security\FileEncryptionService;
use Illuminate\Http\Response;

class DocumentController extends Controller
{
    public function __construct(
        private readonly FileEncryptionService $encryptionService
    ) {
    }

    public function show(string $path): Response
    {
        try {
            $decryptedContents = $this->encryptionService->decryptAndRetrieve($path);

            if ($decryptedContents === null) {
                return response('File not found', 404);
            }

            $mimeType = $this->getMimeType($path);

            return response($decryptedContents, 200)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'private, max-age=3600')
                ->header('X-Content-Type-Options', 'nosniff');
        } catch (\Exception $e) {
            return response('Error retrieving file: ' . $e->getMessage(), 500);
        }
    }

    private function getMimeType(string $path): string
    {
        $originalPath = str_replace('.encrypted', '', $path);
        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);

        return match (strtolower($extension)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };
    }
}
