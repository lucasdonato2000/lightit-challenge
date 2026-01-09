<?php

namespace Tests\Unit\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers;

class StorePatientRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_name_is_required()
    {
        $data = [
            'email' => 'test@gmail.com',
            'phoneCountryCode' => '+1',
            'phoneNumber' => '1234567890',
            'documentPhoto' => TestHelpers::validJpgBase64(),
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fullName']);
    }

    public function test_email_is_required()
    {
        $data = [
            'fullName' => 'Test User',
            'phoneCountryCode' => '+1',
            'phoneNumber' => '1234567890',
            'documentPhoto' => TestHelpers::validJpgBase64(),
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_email_must_be_gmail()
    {
        $data = [
            'fullName' => 'Test User',
            'email' => 'test@yahoo.com',
            'phoneCountryCode' => '+1',
            'phoneNumber' => '1234567890',
            'documentPhoto' => TestHelpers::validJpgBase64(),
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_email_must_be_unique()
    {
        Patient::factory()->create(['email' => 'existing@gmail.com']);

        $data = [
            'fullName' => 'Test User',
            'email' => 'existing@gmail.com',
            'phoneCountryCode' => '+1',
            'phoneNumber' => '9999999999',
            'documentPhoto' => TestHelpers::validJpgBase64(),
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_phone_country_code_is_required()
    {
        $data = [
            'fullName' => 'Test User',
            'email' => 'test@gmail.com',
            'phoneNumber' => '1234567890',
            'documentPhoto' => TestHelpers::validJpgBase64(),
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phoneCountryCode']);
    }

    public function test_phone_number_is_required()
    {
        $data = [
            'fullName' => 'Test User',
            'email' => 'test@gmail.com',
            'phoneCountryCode' => '+1',
            'documentPhoto' => TestHelpers::validJpgBase64(),
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phoneNumber']);
    }

    public function test_phone_number_must_match_pattern()
    {
        $data = [
            'fullName' => 'Test User',
            'email' => 'test@gmail.com',
            'phoneCountryCode' => '+1',
            'phoneNumber' => '123',
            'documentPhoto' => TestHelpers::validJpgBase64(),
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phoneNumber']);
    }

    public function test_phone_combination_must_be_unique()
    {
        Patient::factory()->create([
            'phone_country_code' => '+1',
            'phone_number' => '5555555555'
        ]);

        $data = [
            'fullName' => 'Test User',
            'email' => 'new@gmail.com',
            'phoneCountryCode' => '+1',
            'phoneNumber' => '5555555555',
            'documentPhoto' => TestHelpers::validJpgBase64(),
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phoneNumber']);
    }

    public function test_document_photo_is_required()
    {
        $data = [
            'fullName' => 'Test User',
            'email' => 'test@gmail.com',
            'phoneCountryCode' => '+1',
            'phoneNumber' => '1234567890',
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['documentPhoto']);
    }

    public function test_accepts_different_phone_country_codes()
    {
        $codes = ['+1', '+54', '+598', '+52'];

        foreach ($codes as $index => $code) {
            $data = [
                'fullName' => 'Test User',
                'email' => "test{$index}@gmail.com",
                'phoneCountryCode' => $code,
                'phoneNumber' => '123456' . $index . '890',
                'documentPhoto' => TestHelpers::validJpgBase64(),
            ];

            $response = $this->postJson('/api/patients', $data);
            $response->assertStatus(201);
        }
    }
}
