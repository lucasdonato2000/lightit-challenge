export interface Patient {
  id: string;
  full_name: string;
  email: string;
  phone_country_code: string;
  phone_number: string;
  document_photo_path: string;
  created_at: string;
  updated_at: string;
}

export interface PatientFormData {
  fullName: string;
  email: string;
  phoneCountryCode: string;
  phoneNumber: string;
  documentPhoto: string;
}

export interface ValidationErrors {
  fullName?: string[];
  email?: string[];
  phoneCountryCode?: string[];
  phoneNumber?: string[];
  documentPhoto?: string[];
}

export interface ApiResponse<T> {
  success: boolean;
  message?: string;
  data?: T;
  errors?: ValidationErrors;
}

export interface PaginationInfo {
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;
  from: number | null;
  to: number | null;
}

export interface PaginatedResponse<T> {
  success: boolean;
  data: T[];
  pagination: PaginationInfo;
}
