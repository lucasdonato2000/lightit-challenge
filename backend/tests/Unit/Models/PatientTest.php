<?php

namespace Tests\Unit\Models;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_has_fillable_attributes()
    {
        $patient = new Patient();

        $fillable = [
            'full_name',
            'email',
            'phone_country_code',
            'phone_number',
            'document_photo_path',
        ];

        $this->assertEquals($fillable, $patient->getFillable());
    }

    public function test_patient_uses_uuid()
    {
        $patient = Patient::factory()->create();

        $this->assertIsString($patient->id);
        $this->assertEquals(36, strlen($patient->id));
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $patient->id
        );
    }

    public function test_patient_casts_dates()
    {
        $patient = Patient::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $patient->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $patient->updated_at);
    }

    public function test_get_full_phone_number_attribute()
    {
        $patient = new Patient([
            'phone_country_code' => '+54',
            'phone_number' => '1234567890'
        ]);

        $this->assertEquals('+541234567890', $patient->full_phone_number);
    }

    public function test_patient_can_be_created_with_all_fields()
    {
        $data = [
            'full_name' => 'Test Patient',
            'email' => 'test@example.com',
            'phone_country_code' => '+1',
            'phone_number' => '1234567890',
            'document_photo_path' => 'documents/test.jpg.encrypted',
        ];

        $patient = Patient::create($data);

        $this->assertDatabaseHas('patients', $data);
        $this->assertEquals('Test Patient', $patient->full_name);
    }
}
