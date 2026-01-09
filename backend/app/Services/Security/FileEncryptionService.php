<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileEncryptionService
{
    public function encryptAndStore(string $fileContents, string $directory, string $extension): string
    {
        try {
            $encryptedContents = Crypt::encryptString($fileContents);

            $filename = $directory . '/' . Str::uuid() . '.' . $extension . '.encrypted';

            Storage::disk('local')->put($filename, $encryptedContents);

            return $filename;
        } catch (\Exception $e) {
            throw new \Exception('Failed to encrypt and store file: ' . $e->getMessage());
        }
    }

    public function decryptAndRetrieve(string $encryptedPath): ?string
    {
        try {
            if (!Storage::disk('local')->exists($encryptedPath)) {
                return null;
            }

            $encryptedContents = Storage::disk('local')->get($encryptedPath);

            return Crypt::decryptString($encryptedContents);
        } catch (\Exception $e) {
            throw new \Exception('Failed to decrypt file: ' . $e->getMessage());
        }
    }

    public function deleteEncryptedFile(string $encryptedPath): bool
    {
        try {
            if (Storage::disk('local')->exists($encryptedPath)) {
                return Storage::disk('local')->delete($encryptedPath);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isEncrypted(string $path): bool
    {
        return str_ends_with($path, '.encrypted');
    }
}
