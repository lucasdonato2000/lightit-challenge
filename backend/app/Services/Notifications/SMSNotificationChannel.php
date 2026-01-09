<?php

namespace App\Services\Notifications;

use App\Models\Patient;
use Illuminate\Support\Facades\Log;

class SMSNotificationChannel implements NotificationChannelInterface
{
    public function sendPatientRegistrationConfirmation(Patient $patient): void
    {
        // TODO: Implement when SMS feature is activated
        Log::info('SMS notification would be sent to: ' . $patient->full_phone_number, [
            'patient_id' => $patient->id,
            'patient_name' => $patient->full_name,
        ]);
    }
}
