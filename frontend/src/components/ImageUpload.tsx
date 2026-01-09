import { useState, ChangeEvent, useRef } from 'react';
import { useDragAndDrop } from '@/hooks/useDragAndDrop';
import { validateDocumentPhoto, fileToBase64 } from '@/utils/validation';

interface ImageUploadProps {
  value: string | null;
  onChange: (base64: string) => void;
  error?: string;
}

export const ImageUpload: React.FC<ImageUploadProps> = ({ value, onChange, error }) => {
  const [preview, setPreview] = useState<string | null>(value);
  const [localError, setLocalError] = useState<string | null>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const { isDragging, handleDragEnter, handleDragLeave, handleDragOver, handleDrop } =
    useDragAndDrop();

  const handleFile = async (file: File) => {
    const validationError = validateDocumentPhoto(file);

    if (validationError) {
      setLocalError(validationError);
      setPreview(null);
      return;
    }

    try {
      const base64 = await fileToBase64(file);
      setPreview(base64);
      onChange(base64);
      setLocalError(null);
    } catch (err) {
      setLocalError('Failed to read file');
    }
  };

  const handleFileInput = (e: ChangeEvent<HTMLInputElement>) => {
    const files = e.target.files;
    if (files && files.length > 0) {
      handleFile(files[0]);
    }
  };

  const handleDropFile = (file: File) => {
    handleFile(file);
  };

  const handleClick = () => {
    fileInputRef.current?.click();
  };

  const displayError = error || localError;

  return (
    <div className="space-y-2">
      <div
        className={`
          relative border-2 border-dashed rounded-lg p-4 sm:p-6 transition-all duration-200
          ${isDragging ? 'border-primary-500 bg-primary-50' : 'border-gray-300 bg-white'}
          ${displayError ? 'border-red-500' : ''}
          hover:border-primary-400 cursor-pointer
        `}
        onDragEnter={handleDragEnter}
        onDragLeave={handleDragLeave}
        onDragOver={handleDragOver}
        onDrop={(e) => handleDrop(e, handleDropFile)}
        onClick={handleClick}
      >
        <input
          ref={fileInputRef}
          type="file"
          accept=".jpg,.jpeg,image/jpeg"
          onChange={handleFileInput}
          className="hidden"
        />

        {preview ? (
          <div className="space-y-3">
            <img
              src={preview}
              alt="Document preview"
              className="max-h-48 sm:max-h-64 mx-auto rounded-lg shadow-md"
            />
            <p className="text-xs sm:text-sm text-center text-gray-600">
              Click or drag to replace image
            </p>
          </div>
        ) : (
          <div className="text-center">
            <svg
              className="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 48 48"
              aria-hidden="true"
            >
              <path
                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                strokeWidth={2}
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </svg>
            <div className="mt-3 sm:mt-4">
              <p className="text-xs sm:text-sm font-medium text-gray-900">
                Drop your document photo here, or click to select
              </p>
              <p className="text-xs text-gray-500 mt-1">JPG only, max 5MB</p>
            </div>
          </div>
        )}
      </div>

      {displayError && (
        <p className="text-sm text-red-600 animate-slide-down">{displayError}</p>
      )}
    </div>
  );
};
