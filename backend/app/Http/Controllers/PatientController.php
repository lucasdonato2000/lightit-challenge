<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    public function __construct(
        private readonly PatientService $patientService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $page = max(1, (int) $request->query('page', 1));
            $perPage = min(50, max(5, (int) $request->query('perPage', 10)));

            $paginatedData = $this->patientService->getPaginatedPatients($page, $perPage);

            return response()->json([
                'success' => true,
                'data' => $paginatedData['data'],
                'pagination' => [
                    'current_page' => $paginatedData['current_page'],
                    'per_page' => $paginatedData['per_page'],
                    'total' => $paginatedData['total'],
                    'last_page' => $paginatedData['last_page'],
                    'from' => $paginatedData['from'],
                    'to' => $paginatedData['to'],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching patients: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch patients.',
            ], 500);
        }
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        try {
            $patient = $this->patientService->createPatient($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Patient registered successfully! A confirmation email has been sent.',
                'data' => $patient,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating patient: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to register patient. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $patient = $this->patientService->getPatientById($id);

            if (!$patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $patient,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching patient: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch patient.',
            ], 500);
        }
    }
}
