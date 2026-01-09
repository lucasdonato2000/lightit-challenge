<?php

namespace Tests\Feature;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\TestHelpers;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Queue::fake();
    }

    public function test_can_list_patients_with_pagination()
    {
        Patient::factory()->count(15)->create();

        $response = $this->getJson('/api/patients?page=1&perPage=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'full_name', 'email', 'phone_country_code', 'phone_number']
                ],
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                    'from',
                    'to'
                ]
            ])
            ->assertJson([
                'success' => true,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 15
                ]
            ]);
    }

    public function test_can_create_patient_with_valid_data()
    {
        $data = [
            'fullName' => 'John Doe',
            'email' => 'john@gmail.com',
            'phoneCountryCode' => '+1',
            'phoneNumber' => '1234567890',
            'documentPhoto' => TestHelpers::validJpgBase64()
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'full_name' => 'John Doe',
                    'email' => 'john@gmail.com'
                ]
            ]);

        $this->assertDatabaseHas('patients', [
            'email' => 'john@gmail.com',
            'full_name' => 'John Doe'
        ]);
    }

    public function test_cannot_create_patient_with_duplicate_email()
    {
        Patient::factory()->create(['email' => 'test@gmail.com']);

        $data = [
            'fullName' => 'Jane Doe',
            'email' => 'test@gmail.com',
            'phoneCountryCode' => '+1',
            'phoneNumber' => '9876543210',
            'documentPhoto' => TestHelpers::validJpgBase64()
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_cannot_create_patient_with_duplicate_phone()
    {
        Patient::factory()->create([
            'phone_country_code' => '+1',
            'phone_number' => '1234567890'
        ]);

        $data = [
            'fullName' => 'Jane Doe',
            'email' => 'jane@gmail.com',
            'phoneCountryCode' => '+1',
            'phoneNumber' => '1234567890',
            'documentPhoto' => TestHelpers::validJpgBase64()
        ];

        $response = $this->postJson('/api/patients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phoneNumber']);
    }

    public function test_validates_required_fields()
    {
        $response = $this->postJson('/api/patients', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'fullName',
                'email',
                'phoneCountryCode',
                'phoneNumber',
                'documentPhoto'
            ]);
    }

    public function test_can_show_single_patient()
    {
        $patient = Patient::factory()->create();

        $response = $this->getJson("/api/patients/{$patient->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $patient->id,
                    'email' => $patient->email
                ]
            ]);
    }

    public function test_returns_404_for_nonexistent_patient()
    {
        $fakeUuid = '550e8400-e29b-41d4-a716-446655440000';
        $response = $this->getJson("/api/patients/{$fakeUuid}");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Patient not found.'
            ]);
    }

    public function test_pagination_respects_per_page_limits()
    {
        Patient::factory()->count(100)->create();

        $response = $this->getJson('/api/patients?page=1&perPage=5');
        $response->assertJson(['pagination' => ['per_page' => 5]]);

        $response = $this->getJson('/api/patients?page=1&perPage=60');
        $response->assertJson(['pagination' => ['per_page' => 50]]);

        $response = $this->getJson('/api/patients?page=1&perPage=2');
        $response->assertJson(['pagination' => ['per_page' => 5]]);
    }
}
