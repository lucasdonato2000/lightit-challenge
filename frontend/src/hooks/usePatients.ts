import { useState, useEffect, useCallback } from 'react';
import type { Patient, PaginationInfo } from '@/types/patient';
import { apiService } from '@/services/api';

interface UsePatientsResult {
  patients: Patient[];
  pagination: PaginationInfo | null;
  loading: boolean;
  error: string | null;
  refetch: () => Promise<void>;
  setPage: (page: number) => void;
  setPerPage: (perPage: number) => void;
}

export const usePatients = (): UsePatientsResult => {
  const [patients, setPatients] = useState<Patient[]>([]);
  const [pagination, setPagination] = useState<PaginationInfo | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(10);

  const fetchPatients = useCallback(async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await apiService.getPaginatedPatients(page, perPage);
      setPatients(response.data);
      setPagination(response.pagination);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load patients');
    } finally {
      setLoading(false);
    }
  }, [page, perPage]);

  useEffect(() => {
    fetchPatients();
  }, [fetchPatients]);

  const handleSetPage = useCallback((newPage: number) => {
    setPage(newPage);
  }, []);

  const handleSetPerPage = useCallback((newPerPage: number) => {
    setPerPage(newPerPage);
    setPage(1);
  }, []);

  return {
    patients,
    pagination,
    loading,
    error,
    refetch: fetchPatients,
    setPage: handleSetPage,
    setPerPage: handleSetPerPage,
  };
};
