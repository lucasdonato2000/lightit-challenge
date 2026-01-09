<?php

namespace App\Services\Notifications;

use App\Models\Patient;

interface NotificationServiceInterface
{
    public function sendPatientRegistrationConfirmation(Patient $patient): void;
}
