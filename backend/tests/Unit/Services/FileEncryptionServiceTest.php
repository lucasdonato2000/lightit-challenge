<?php

namespace Tests\Unit\Services;

use App\Services\Security\FileEncryptionService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileEncryptionServiceTest extends TestCase
{
    protected FileEncryptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->service = new FileEncryptionService();
    }

    public function test_can_encrypt_and_store_file()
    {
        $fileContents = 'test file content';
        $directory = 'documents';
        $extension = 'txt';

        $path = $this->service->encryptAndStore($fileContents, $directory, $extension);

        $this->assertStringStartsWith($directory . '/', $path);
        $this->assertStringEndsWith('.' . $extension . '.encrypted', $path);
        Storage::disk('local')->assertExists($path);

        $encryptedContents = Storage::disk('local')->get($path);
        $decrypted = Crypt::decryptString($encryptedContents);
        $this->assertEquals($fileContents, $decrypted);
    }

    public function test_can_decrypt_and_retrieve_file()
    {
        $originalContent = 'secret content';
        $encryptedContent = Crypt::encryptString($originalContent);
        $path = 'documents/test.txt.encrypted';

        Storage::disk('local')->put($path, $encryptedContent);

        $decryptedContent = $this->service->decryptAndRetrieve($path);

        $this->assertEquals($originalContent, $decryptedContent);
    }

    public function test_returns_null_for_nonexistent_file()
    {
        $result = $this->service->decryptAndRetrieve('nonexistent/file.txt.encrypted');

        $this->assertNull($result);
    }

    public function test_generates_unique_filenames()
    {
        $content = 'test';

        $path1 = $this->service->encryptAndStore($content, 'docs', 'txt');
        $path2 = $this->service->encryptAndStore($content, 'docs', 'txt');

        $this->assertNotEquals($path1, $path2);
    }

    public function test_encryption_is_secure()
    {
        $originalContent = 'sensitive data';
        $path = $this->service->encryptAndStore($originalContent, 'secure', 'dat');

        $storedContent = Storage::disk('local')->get($path);

        $this->assertNotEquals($originalContent, $storedContent);
        $this->assertStringNotContainsString('sensitive', $storedContent);
    }
}
