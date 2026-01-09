<?php

namespace Tests\Unit\Services;

use App\Models\Patient;
use App\Repositories\PatientRepositoryInterface;
use App\Services\Notifications\NotificationServiceInterface;
use App\Services\PatientService;
use App\Services\Security\FileEncryptionService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class PatientServiceTest extends TestCase
{
    protected PatientService $service;
    protected $patientRepository;
    protected $notificationService;
    protected $encryptionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->patientRepository = Mockery::mock(PatientRepositoryInterface::class);
        $this->notificationService = Mockery::mock(NotificationServiceInterface::class);
        $this->encryptionService = Mockery::mock(FileEncryptionService::class);

        $this->service = new PatientService(
            $this->patientRepository,
            $this->notificationService,
            $this->encryptionService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_create_patient()
    {
        $data = [
            'fullName' => 'John Doe',
            'email' => 'john@example.com',
            'phoneCountryCode' => '+1',
            'phoneNumber' => '1234567890',
            'documentPhoto' => 'data:image/jpeg;base64,' . base64_encode('fake-image')
        ];

        $encryptedPath = 'documents/uuid.jpg.encrypted';

        $this->encryptionService
            ->shouldReceive('encryptAndStore')
            ->once()
            ->andReturn($encryptedPath);

        $patient = new Patient([
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_country_code' => '+1',
            'phone_number' => '1234567890',
            'document_photo_path' => $encryptedPath,
        ]);

        $this->patientRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($encryptedPath) {
                return $arg['full_name'] === 'John Doe'
                    && $arg['email'] === 'john@example.com'
                    && $arg['document_photo_path'] === $encryptedPath;
            }))
            ->andReturn($patient);

        $this->notificationService
            ->shouldReceive('sendPatientRegistrationConfirmation')
            ->once()
            ->with($patient);

        $result = $this->service->createPatient($data);

        $this->assertInstanceOf(Patient::class, $result);
        $this->assertEquals('John Doe', $result->full_name);
    }

    public function test_get_all_patients_returns_collection()
    {
        $patients = new Collection([new Patient(), new Patient()]);

        $this->patientRepository
            ->shouldReceive('findAll')
            ->once()
            ->andReturn($patients);

        $result = $this->service->getAllPatients();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function test_get_paginated_patients()
    {
        $paginatedData = [
            'data' => [new Patient(), new Patient()],
            'current_page' => 1,
            'per_page' => 10,
            'total' => 2,
            'last_page' => 1,
            'from' => 1,
            'to' => 2,
        ];

        $this->patientRepository
            ->shouldReceive('findPaginated')
            ->once()
            ->with(1, 10)
            ->andReturn($paginatedData);

        $result = $this->service->getPaginatedPatients(1, 10);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['current_page']);
        $this->assertEquals(10, $result['per_page']);
    }

    public function test_get_patient_by_id()
    {
        $patient = new Patient(['id' => 'test-id']);

        $this->patientRepository
            ->shouldReceive('findById')
            ->once()
            ->with('test-id')
            ->andReturn($patient);

        $result = $this->service->getPatientById('test-id');

        $this->assertInstanceOf(Patient::class, $result);
    }

    public function test_is_email_registered_returns_true_when_exists()
    {
        $this->patientRepository
            ->shouldReceive('emailExists')
            ->once()
            ->with('test@example.com')
            ->andReturn(true);

        $result = $this->service->isEmailRegistered('test@example.com');

        $this->assertTrue($result);
    }

    public function test_is_email_registered_returns_false_when_not_exists()
    {
        $this->patientRepository
            ->shouldReceive('emailExists')
            ->once()
            ->with('new@example.com')
            ->andReturn(false);

        $result = $this->service->isEmailRegistered('new@example.com');

        $this->assertFalse($result);
    }
}
