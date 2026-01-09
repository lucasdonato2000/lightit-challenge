export const validateFullName = (name: string): string | null => {
  if (!name.trim()) {
    return 'Full name is required';
  }

  if (!/^[a-zA-Z\s]+$/.test(name)) {
    return 'Full name must contain only letters and spaces';
  }

  if (name.length > 255) {
    return 'Full name must not exceed 255 characters';
  }

  return null;
};

export const validateEmail = (email: string): string | null => {
  if (!email.trim()) {
    return 'Email is required';
  }

  if (!/^[a-zA-Z0-9._%+-]+@gmail\.com$/.test(email)) {
    return 'Only @gmail.com email addresses are accepted';
  }

  return null;
};

export const validatePhoneCountryCode = (code: string): string | null => {
  if (!code.trim()) {
    return 'Country code is required';
  }

  if (!/^\+\d{1,3}$/.test(code)) {
    return 'Country code must be in the format +XXX';
  }

  return null;
};

export const validatePhoneNumber = (number: string): string | null => {
  if (!number.trim()) {
    return 'Phone number is required';
  }

  if (!/^\d{7,15}$/.test(number)) {
    return 'Phone number must contain only digits (7-15 characters)';
  }

  return null;
};

export const validateDocumentPhoto = (file: File | null): string | null => {
  if (!file) {
    return 'Document photo is required';
  }

  if (file.type !== 'image/jpeg' && file.type !== 'image/jpg') {
    return 'Only JPG images are accepted';
  }

  const maxSize = 5 * 1024 * 1024; // 5MB
  if (file.size > maxSize) {
    return 'Image must not exceed 5MB';
  }

  return null;
};

export const fileToBase64 = (file: File): Promise<string> => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
      if (typeof reader.result === 'string') {
        resolve(reader.result);
      } else {
        reject(new Error('Failed to read file'));
      }
    };
    reader.onerror = (error) => reject(error);
  });
};
