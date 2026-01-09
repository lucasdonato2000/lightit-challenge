import { useState } from 'react';
import type { Patient } from '@/types/patient';
import { API_BASE_URL } from '@/services/api';

interface PatientCardProps {
  patient: Patient;
}

export const PatientCard: React.FC<PatientCardProps> = ({ patient }) => {
  const [isExpanded, setIsExpanded] = useState(false);

  const toggleExpanded = () => {
    setIsExpanded((prev) => !prev);
  };

  const formattedDate = new Date(patient.created_at).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });

  const imageUrl = `${API_BASE_URL}/documents/${patient.document_photo_path}`;

  return (
    <div
      className="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 cursor-pointer mb-6 break-inside-avoid"
      onClick={toggleExpanded}
    >
      <div className="p-4 sm:p-6">
        <div className="flex items-start gap-4">
          <div className="flex-shrink-0">
            <img
              src={imageUrl}
              alt={`${patient.full_name}'s document`}
              className="w-24 h-16 sm:w-32 sm:h-20 rounded-lg object-cover border-2 border-gray-200 shadow-sm"
              onError={(e) => {
                e.currentTarget.src =
                  'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="128" height="80" viewBox="0 0 24 15" fill="none" stroke="%23cbd5e1" stroke-width="2"%3E%3Crect x="1" y="1" width="22" height="13" rx="2"/%3E%3Ccircle cx="6" cy="6" r="1.5"/%3E%3Cpath d="m23 11-5-5L1 13"/%3E%3C/svg%3E';
              }}
            />
          </div>

          <div className="flex-1 min-w-0">
            <h3 className="text-lg font-semibold text-gray-900 truncate">
              {patient.full_name}
            </h3>
            <p className="text-sm text-gray-500 mt-1">
              {isExpanded ? 'Click to collapse' : 'Click to expand'}
            </p>
          </div>

          <div className="flex-shrink-0">
            <svg
              className={`w-6 h-6 text-gray-400 transition-transform duration-300 ${
                isExpanded ? 'rotate-180' : ''
              }`}
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </div>
        </div>

        {isExpanded && (
          <div className="mt-6 pt-6 border-t border-gray-200 space-y-4 animate-slide-down">
            <div className="flex items-start">
              <svg
                className="w-5 h-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                />
              </svg>
              <div>
                <p className="text-xs text-gray-500 uppercase tracking-wide font-medium">
                  Email
                </p>
                <p className="text-sm text-gray-900 mt-1">{patient.email}</p>
              </div>
            </div>

            <div className="flex items-start">
              <svg
                className="w-5 h-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                />
              </svg>
              <div>
                <p className="text-xs text-gray-500 uppercase tracking-wide font-medium">
                  Phone
                </p>
                <p className="text-sm text-gray-900 mt-1">
                  {patient.phone_country_code} {patient.phone_number}
                </p>
              </div>
            </div>

            <div className="flex items-start">
              <svg
                className="w-5 h-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
              <div>
                <p className="text-xs text-gray-500 uppercase tracking-wide font-medium">
                  Registered
                </p>
                <p className="text-sm text-gray-900 mt-1">{formattedDate}</p>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
