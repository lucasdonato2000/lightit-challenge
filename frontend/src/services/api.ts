import type { Patient, PatientFormData, ApiResponse, PaginatedResponse } from '@/types/patient';

export const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

class ApiService {
  private baseUrl: string;

  constructor(baseUrl: string) {
    this.baseUrl = baseUrl;
  }

  async getPatients(): Promise<Patient[]> {
    const response = await fetch(`${this.baseUrl}/patients`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error('Failed to fetch patients');
    }

    const result: ApiResponse<Patient[]> = await response.json();

    if (!result.success || !result.data) {
      throw new Error(result.message || 'Failed to fetch patients');
    }

    return result.data;
  }

  async getPaginatedPatients(page: number = 1, perPage: number = 10): Promise<PaginatedResponse<Patient>> {
    const response = await fetch(`${this.baseUrl}/patients?page=${page}&perPage=${perPage}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error('Failed to fetch patients');
    }

    const result: PaginatedResponse<Patient> = await response.json();

    if (!result.success || !result.data) {
      throw new Error('Failed to fetch patients');
    }

    return result;
  }

  async createPatient(data: PatientFormData): Promise<ApiResponse<Patient>> {
    const response = await fetch(`${this.baseUrl}/patients`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });

    const result: ApiResponse<Patient> = await response.json();

    if (!response.ok) {
      return result;
    }

    return result;
  }

  async getPatient(id: string): Promise<Patient> {
    const response = await fetch(`${this.baseUrl}/patients/${id}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error('Failed to fetch patient');
    }

    const result: ApiResponse<Patient> = await response.json();

    if (!result.success || !result.data) {
      throw new Error(result.message || 'Failed to fetch patient');
    }

    return result.data;
  }
}

export const apiService = new ApiService(API_BASE_URL);
