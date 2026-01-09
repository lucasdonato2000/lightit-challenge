import { useState } from 'react';
import { PatientCard } from './components/PatientCard';
import { PatientForm } from './components/PatientForm';
import { Pagination } from './components/Pagination';
import { Modal } from './components/Modal';
import { usePatients } from './hooks/usePatients';
import { useModal } from './hooks/useModal';
import { apiService } from './services/api';
import type { PatientFormData, ValidationErrors } from './types/patient';

function App() {
  const { patients, pagination, loading, error, refetch, setPage, setPerPage } = usePatients();
  const [showForm, setShowForm] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [serverErrors, setServerErrors] = useState<ValidationErrors | undefined>();

  const successModal = useModal();
  const errorModal = useModal();
  const [modalMessage, setModalMessage] = useState('');

  const handleAddPatient = () => {
    setShowForm(true);
    setServerErrors(undefined);
  };

  const handleCancelForm = () => {
    setShowForm(false);
    setServerErrors(undefined);
  };

  const handleSubmitForm = async (data: PatientFormData) => {
    setIsSubmitting(true);
    setServerErrors(undefined);

    try {
      const response = await apiService.createPatient(data);

      if (response.success) {
        setShowForm(false);
        setModalMessage(response.message || 'Patient registered successfully!');
        successModal.open();
        await refetch();
      } else {
        if (response.errors) {
          setServerErrors(response.errors);
        } else {
          setModalMessage(response.message || 'Failed to register patient');
          errorModal.open();
        }
      }
    } catch (err) {
      setModalMessage(
        err instanceof Error ? err.message : 'An unexpected error occurred'
      );
      errorModal.open();
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary-50 via-white to-primary-50 flex flex-col">
      <header className="bg-slate-800 shadow-lg">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
          <div className="flex items-center justify-between gap-3">
            <div className="min-w-0 flex-1">
              <h1 className="text-xl sm:text-2xl lg:text-3xl font-bold text-white truncate">
                Patient Registration
              </h1>
              <p className="text-slate-300 text-xs sm:text-sm mt-0.5 hidden sm:block">Manage patient records efficiently</p>
            </div>
            {!showForm && (
              <button
                onClick={handleAddPatient}
                className="px-3 py-2 sm:px-4 sm:py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors shadow-md hover:shadow-lg flex items-center gap-1.5 whitespace-nowrap flex-shrink-0"
              >
                <svg
                  className="w-4 h-4 sm:w-5 sm:h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M12 4v16m8-8H4"
                  />
                </svg>
                <span className="hidden sm:inline">Add Patient</span>
                <span className="sm:hidden">Add</span>
              </button>
            )}
          </div>
        </div>
      </header>

      <main className="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8 w-full">{showForm ? (
          <div className="max-w-2xl mx-auto">
            <div className="bg-white rounded-xl shadow-md p-4 sm:p-6 lg:p-8">
              <h2 className="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">
                Register New Patient
              </h2>
              <PatientForm
                onSubmit={handleSubmitForm}
                onCancel={handleCancelForm}
                isSubmitting={isSubmitting}
                serverErrors={serverErrors}
              />
            </div>
          </div>
        ) : (
          <>
            {loading ? (
              <div className="flex flex-col items-center justify-center py-12 sm:py-16 lg:py-20">
                <div className="animate-spin rounded-full h-12 w-12 sm:h-16 sm:w-16 border-b-2 border-primary-600"></div>
                <p className="text-gray-600 mt-4 text-sm sm:text-base">Loading patients...</p>
              </div>
            ) : error ? (
              <div className="flex flex-col items-center justify-center py-12 sm:py-16 lg:py-20 px-4">
                <svg
                  className="w-12 h-12 sm:w-16 sm:h-16 text-red-500 mb-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                <p className="text-gray-900 font-medium text-base sm:text-lg text-center">{error}</p>
                <button
                  onClick={refetch}
                  className="mt-4 px-4 py-2 sm:px-6 sm:py-2 bg-primary-600 text-white rounded-lg text-sm sm:text-base hover:bg-primary-700 transition-colors"
                >
                  Try Again
                </button>
              </div>
            ) : patients.length === 0 ? (
              <div className="flex flex-col items-center justify-center py-12 sm:py-16 lg:py-20 px-4">
                <svg
                  className="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 text-gray-300 mb-4"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={1.5}
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                  />
                </svg>
                <h3 className="text-lg sm:text-xl font-semibold text-gray-900 mb-2 text-center">
                  No patients yet
                </h3>
                <p className="text-gray-600 mb-6 text-sm sm:text-base text-center">
                  Get started by adding your first patient
                </p>
                <button
                  onClick={handleAddPatient}
                  className="px-4 py-2 sm:px-6 sm:py-3 bg-primary-600 text-white rounded-lg text-sm sm:text-base font-medium hover:bg-primary-700 transition-colors"
                >
                  Add First Patient
                </button>
              </div>
            ) : (
              <>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 auto-rows-fr">
                  {patients.map((patient) => (
                    <PatientCard key={patient.id} patient={patient} />
                  ))}
                </div>
                {pagination && (
                  <Pagination
                    pagination={pagination}
                    onPageChange={setPage}
                    onPerPageChange={setPerPage}
                  />
                )}
              </>
            )}
          </>
        )}
      </main>

      <Modal
        isOpen={successModal.isOpen}
        onClose={successModal.close}
        type="success"
        title="Success!"
        message={modalMessage}
      />

      <Modal
        isOpen={errorModal.isOpen}
        onClose={errorModal.close}
        type="error"
        title="Error"
        message={modalMessage}
      />
    </div>
  );
}

export default App;
