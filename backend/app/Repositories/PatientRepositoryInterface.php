<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

interface PatientRepositoryInterface
{
    public function create(array $data): Patient;

    public function findByEmail(string $email): ?Patient;

    public function findAll(): Collection;

    public function findPaginated(int $page, int $perPage): array;

    public function findById(string $id): ?Patient;

    public function emailExists(string $email): bool;
}
