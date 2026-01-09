<?php

namespace Tests\Unit\Repositories;

use App\Models\Patient;
use App\Repositories\EloquentPatientRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentPatientRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EloquentPatientRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentPatientRepository();
    }

    public function test_can_create_patient()
    {
        $data = [
            'full_name' => 'Jane Doe',
            'email' => 'jane@gmail.com',
            'phone_country_code' => '+1',
            'phone_number' => '9876543210',
            'document_photo_path' => 'documents/test.jpg.encrypted',
        ];

        $patient = $this->repository->create($data);

        $this->assertInstanceOf(Patient::class, $patient);
        $this->assertEquals('Jane Doe', $patient->full_name);
        $this->assertDatabaseHas('patients', ['email' => 'jane@gmail.com']);
    }

    public function test_can_find_patient_by_email()
    {
        $created = Patient::factory()->create(['email' => 'find@gmail.com']);

        $found = $this->repository->findByEmail('find@gmail.com');

        $this->assertNotNull($found);
        $this->assertEquals($created->id, $found->id);
    }

    public function test_find_by_email_returns_null_when_not_found()
    {
        $found = $this->repository->findByEmail('nonexistent@gmail.com');

        $this->assertNull($found);
    }

    public function test_find_all_returns_patients_ordered_by_latest()
    {
        Patient::factory()->create(['created_at' => now()->subDays(2)]);
        $latest = Patient::factory()->create(['created_at' => now()]);
        Patient::factory()->create(['created_at' => now()->subDay()]);

        $patients = $this->repository->findAll();

        $this->assertCount(3, $patients);
        $this->assertEquals($latest->id, $patients->first()->id);
    }

    public function test_find_paginated_returns_correct_structure()
    {
        Patient::factory()->count(25)->create();

        $result = $this->repository->findPaginated(1, 10);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('current_page', $result);
        $this->assertArrayHasKey('per_page', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('last_page', $result);
        $this->assertCount(10, $result['data']);
        $this->assertEquals(25, $result['total']);
        $this->assertEquals(3, $result['last_page']);
    }

    public function test_find_paginated_respects_page_parameter()
    {
        Patient::factory()->count(15)->create();

        $page1 = $this->repository->findPaginated(1, 5);
        $page2 = $this->repository->findPaginated(2, 5);

        $this->assertEquals(1, $page1['current_page']);
        $this->assertEquals(2, $page2['current_page']);
        $this->assertNotEquals($page1['data'][0]->id, $page2['data'][0]->id);
    }

    public function test_can_find_patient_by_id()
    {
        $patient = Patient::factory()->create();

        $found = $this->repository->findById($patient->id);

        $this->assertNotNull($found);
        $this->assertEquals($patient->id, $found->id);
    }

    public function test_find_by_id_returns_null_when_not_found()
    {
        $fakeUuid = '550e8400-e29b-41d4-a716-446655440000';
        $found = $this->repository->findById($fakeUuid);

        $this->assertNull($found);
    }

    public function test_email_exists_returns_true_when_exists()
    {
        Patient::factory()->create(['email' => 'exists@gmail.com']);

        $exists = $this->repository->emailExists('exists@gmail.com');

        $this->assertTrue($exists);
    }

    public function test_email_exists_returns_false_when_not_exists()
    {
        $exists = $this->repository->emailExists('notfound@gmail.com');

        $this->assertFalse($exists);
    }
}
