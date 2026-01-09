<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullName' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
                'unique:patients,email',
                'max:255',
            ],
            'phoneCountryCode' => [
                'required',
                'string',
                'regex:/^\+\d{1,3}$/',
            ],
            'phoneNumber' => [
                'required',
                'string',
                'regex:/^\d{7,15}$/',
                function ($attribute, $value, $fail) {
                    $phoneCountryCode = $this->input('phoneCountryCode');

                    $exists = \App\Models\Patient::where('phone_country_code', $phoneCountryCode)
                        ->where('phone_number', $value)
                        ->exists();

                    if ($exists) {
                        $fail('This phone number is already registered.');
                    }
                },
            ],
            'documentPhoto' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!$this->isValidBase64Image($value)) {
                        $fail('The document photo must be a valid JPG image.');
                        return;
                    }

                    $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $value);
                    if ($imageData !== null) {
                        $decodedImage = base64_decode($imageData, true);
                        if ($decodedImage !== false) {
                            $sizeInBytes = strlen($decodedImage);
                            $sizeInMB = $sizeInBytes / 1024 / 1024;

                            if ($sizeInMB > 5) {
                                $fail('The document photo must not exceed 5MB.');
                            }
                        }
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fullName.required' => 'The full name field is required.',
            'fullName.regex' => 'The full name must contain only letters and spaces.',
            'email.required' => 'The email address is required.',
            'email.regex' => 'Only @gmail.com email addresses are accepted.',
            'email.unique' => 'This email address is already registered.',
            'phoneCountryCode.required' => 'The phone country code is required.',
            'phoneCountryCode.regex' => 'The phone country code must be in the format +XXX.',
            'phoneNumber.required' => 'The phone number is required.',
            'phoneNumber.regex' => 'The phone number must contain only digits (7-15 characters).',
            'documentPhoto.required' => 'The document photo is required.',
        ];
    }

    private function isValidBase64Image(string $base64String): bool
    {
        try {
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);

            if ($imageData === null || $imageData === $base64String) {
                $imageData = $base64String;
            }

            $decodedImage = base64_decode($imageData, true);

            if ($decodedImage === false) {
                return false;
            }

            $imageInfo = @getimagesizefromstring($decodedImage);

            if ($imageInfo === false) {
                return false;
            }

            if (!in_array($imageInfo['mime'], ['image/jpeg', 'image/jpg'])) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
