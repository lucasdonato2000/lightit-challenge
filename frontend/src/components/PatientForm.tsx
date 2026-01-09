import { useState, FormEvent } from 'react';
import type { PatientFormData, ValidationErrors } from '@/types/patient';
import {
  validateFullName,
  validateEmail,
  validatePhoneCountryCode,
  validatePhoneNumber,
} from '@/utils/validation';
import { ImageUpload } from './ImageUpload';

interface PatientFormProps {
  onSubmit: (data: PatientFormData) => Promise<void>;
  onCancel: () => void;
  isSubmitting: boolean;
  serverErrors?: ValidationErrors;
}

export const PatientForm: React.FC<PatientFormProps> = ({
  onSubmit,
  onCancel,
  isSubmitting,
  serverErrors,
}) => {
  const [formData, setFormData] = useState<PatientFormData>({
    fullName: '',
    email: '',
    phoneCountryCode: '',
    phoneNumber: '',
    documentPhoto: '',
  });

  const [errors, setErrors] = useState<Record<string, string>>({});
  const [touched, setTouched] = useState<Record<string, boolean>>({});

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();

    setTouched({
      fullName: true,
      email: true,
      phoneCountryCode: true,
      phoneNumber: true,
      documentPhoto: true,
    });

    const validationErrors: Record<string, string> = {};

    const nameError = validateFullName(formData.fullName);
    if (nameError) validationErrors.fullName = nameError;

    const emailError = validateEmail(formData.email);
    if (emailError) validationErrors.email = emailError;

    const countryCodeError = validatePhoneCountryCode(formData.phoneCountryCode);
    if (countryCodeError) validationErrors.phoneCountryCode = countryCodeError;

    const phoneError = validatePhoneNumber(formData.phoneNumber);
    if (phoneError) validationErrors.phoneNumber = phoneError;

    if (!formData.documentPhoto) {
      validationErrors.documentPhoto = 'Document photo is required';
    }

    setErrors(validationErrors);

    if (Object.keys(validationErrors).length === 0) {
      await onSubmit(formData);
    }
  };

  const handleChange = (field: keyof PatientFormData, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));

    if (errors[field]) {
      setErrors((prev) => {
        const newErrors = { ...prev };
        delete newErrors[field];
        return newErrors;
      });
    }
  };

  const handleBlur = (field: keyof PatientFormData) => {
    setTouched((prev) => ({ ...prev, [field]: true }));
  };

  const getError = (field: keyof PatientFormData): string | undefined => {
    if (!touched[field]) return undefined;

    if (serverErrors && serverErrors[field]) {
      return serverErrors[field][0];
    }

    return errors[field];
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4 sm:space-y-6">
      <div>
        <label htmlFor="fullName" className="block text-sm font-medium text-gray-700 mb-1">
          Full Name *
        </label>
        <input
          id="fullName"
          type="text"
          value={formData.fullName}
          onChange={(e) => handleChange('fullName', e.target.value)}
          onBlur={() => handleBlur('fullName')}
          className={`
            w-full px-3 py-2 sm:px-4 sm:py-2 text-sm sm:text-base border rounded-lg focus:outline-none focus:ring-2
            ${getError('fullName') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary-500'}
          `}
          placeholder="John Doe"
          disabled={isSubmitting}
        />
        {getError('fullName') && (
          <p className="mt-1 text-sm text-red-600 animate-slide-down">{getError('fullName')}</p>
        )}
      </div>

      <div>
        <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
          Email Address *
        </label>
        <input
          id="email"
          type="email"
          value={formData.email}
          onChange={(e) => handleChange('email', e.target.value)}
          onBlur={() => handleBlur('email')}
          className={`
            w-full px-3 py-2 sm:px-4 sm:py-2 text-sm sm:text-base border rounded-lg focus:outline-none focus:ring-2
            ${getError('email') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary-500'}
          `}
          placeholder="john@gmail.com"
          disabled={isSubmitting}
        />
        {getError('email') && (
          <p className="mt-1 text-sm text-red-600 animate-slide-down">{getError('email')}</p>
        )}
        <p className="mt-1 text-xs text-gray-500">Only @gmail.com addresses are accepted</p>
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
        <div className="grid grid-cols-3 gap-2 sm:gap-3">
          <div className="col-span-1">
            <input
              type="text"
              value={formData.phoneCountryCode}
              onChange={(e) => handleChange('phoneCountryCode', e.target.value)}
              onBlur={() => handleBlur('phoneCountryCode')}
              className={`
                w-full px-2 py-2 sm:px-4 sm:py-2 text-sm sm:text-base border rounded-lg focus:outline-none focus:ring-2
                ${getError('phoneCountryCode') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary-500'}
              `}
              placeholder="+1"
              disabled={isSubmitting}
            />
          </div>
          <div className="col-span-2">
            <input
              type="text"
              value={formData.phoneNumber}
              onChange={(e) => handleChange('phoneNumber', e.target.value)}
              onBlur={() => handleBlur('phoneNumber')}
              className={`
                w-full px-3 py-2 sm:px-4 sm:py-2 text-sm sm:text-base border rounded-lg focus:outline-none focus:ring-2
                ${getError('phoneNumber') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary-500'}
              `}
              placeholder="5551234567"
              disabled={isSubmitting}
            />
          </div>
        </div>
        {getError('phoneCountryCode') && (
          <p className="mt-1 text-sm text-red-600 animate-slide-down">
            {getError('phoneCountryCode')}
          </p>
        )}
        {getError('phoneNumber') && (
          <p className="mt-1 text-sm text-red-600 animate-slide-down">
            {getError('phoneNumber')}
          </p>
        )}
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">
          Document Photo *
        </label>
        <ImageUpload
          value={formData.documentPhoto}
          onChange={(base64) => handleChange('documentPhoto', base64)}
          error={getError('documentPhoto')}
        />
      </div>

      <div className="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
        <button
          type="button"
          onClick={onCancel}
          disabled={isSubmitting}
          className="flex-1 px-4 py-2 sm:px-6 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Cancel
        </button>
        <button
          type="submit"
          disabled={isSubmitting}
          className="flex-1 px-4 py-2 sm:px-6 sm:py-3 text-sm sm:text-base bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
        >
          {isSubmitting ? (
            <>
              <svg
                className="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  className="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  strokeWidth="4"
                ></circle>
                <path
                  className="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              Registering...
            </>
          ) : (
            'Register Patient'
          )}
        </button>
      </div>
    </form>
  );
};
