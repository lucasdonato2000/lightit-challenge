<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

class EloquentPatientRepository implements PatientRepositoryInterface
{
    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    public function findByEmail(string $email): ?Patient
    {
        return Patient::where('email', $email)->first();
    }

    public function findAll(): Collection
    {
        return Patient::orderBy('created_at', 'desc')->get();
    }

    public function findPaginated(int $page, int $perPage): array
    {
        $paginator = Patient::orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    public function findById(string $id): ?Patient
    {
        return Patient::find($id);
    }

    public function emailExists(string $email): bool
    {
        return Patient::where('email', $email)->exists();
    }
}
