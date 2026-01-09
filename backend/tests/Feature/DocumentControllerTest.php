<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_can_retrieve_encrypted_document()
    {
        $imageContent = 'fake-image-binary-data';
        $encryptedContent = Crypt::encryptString($imageContent);
        $path = 'documents/test.jpg.encrypted';

        Storage::disk('local')->put($path, $encryptedContent);

        $response = $this->get("/api/documents/{$path}");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('private', $cacheControl);
        $this->assertStringContainsString('max-age=3600', $cacheControl);

        $this->assertEquals($imageContent, $response->getContent());
    }

    public function test_returns_404_for_nonexistent_document()
    {
        $response = $this->get('/api/documents/nonexistent/file.jpg.encrypted');

        $response->assertStatus(404);
    }

    public function test_document_has_security_headers()
    {
        $imageContent = 'test-image';
        $encryptedContent = Crypt::encryptString($imageContent);
        $path = 'documents/secure.jpg.encrypted';

        Storage::disk('local')->put($path, $encryptedContent);

        $response = $this->get("/api/documents/{$path}");

        $response->assertStatus(200)
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_supports_nested_paths()
    {
        $imageContent = 'nested-image';
        $encryptedContent = Crypt::encryptString($imageContent);
        $path = 'documents/subfolder/test.png.encrypted';

        Storage::disk('local')->put($path, $encryptedContent);

        $response = $this->get("/api/documents/{$path}");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'image/png');
    }
}
