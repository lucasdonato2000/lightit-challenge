<?php

namespace App\Services;

use App\Models\Patient;
use App\Repositories\PatientRepositoryInterface;
use App\Services\Notifications\NotificationServiceInterface;
use App\Services\Security\FileEncryptionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatientService
{
    public function __construct(
        private readonly PatientRepositoryInterface $patientRepository,
        private readonly NotificationServiceInterface $notificationService,
        private readonly FileEncryptionService $encryptionService
    ) {
    }

    public function createPatient(array $data): Patient
    {
        $documentPhotoPath = $this->storeDocumentPhoto($data['documentPhoto']);

        $patientData = [
            'full_name' => $data['fullName'],
            'email' => $data['email'],
            'phone_country_code' => $data['phoneCountryCode'],
            'phone_number' => $data['phoneNumber'],
            'document_photo_path' => $documentPhotoPath,
        ];

        $patient = $this->patientRepository->create($patientData);

        $this->notificationService->sendPatientRegistrationConfirmation($patient);

        return $patient;
    }

    public function getAllPatients(): Collection
    {
        return $this->patientRepository->findAll();
    }

    public function getPaginatedPatients(int $page, int $perPage): array
    {
        return $this->patientRepository->findPaginated($page, $perPage);
    }

    public function getPatientById(string $id): ?Patient
    {
        return $this->patientRepository->findById($id);
    }

    public function isEmailRegistered(string $email): bool
    {
        return $this->patientRepository->emailExists($email);
    }

    private function storeDocumentPhoto(string $base64Image): string
    {
        try {
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);

            if ($image === null) {
                throw new \Exception('Invalid image format');
            }

            $decodedImage = base64_decode($image, true);

            if ($decodedImage === false) {
                throw new \Exception('Failed to decode image');
            }

            $encryptedPath = $this->encryptionService->encryptAndStore(
                $decodedImage,
                'documents',
                'jpg'
            );

            return $encryptedPath;
        } catch (\Exception $e) {
            throw new \Exception('Failed to store document photo: ' . $e->getMessage());
        }
    }
}
