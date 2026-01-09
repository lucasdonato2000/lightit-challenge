<?php

namespace App\Services\Notifications;

use App\Jobs\SendPatientWelcomeEmail;
use App\Models\Patient;

class EmailNotificationChannel implements NotificationChannelInterface
{
    public function sendPatientRegistrationConfirmation(Patient $patient): void
    {
        SendPatientWelcomeEmail::dispatch($patient);
    }
}
