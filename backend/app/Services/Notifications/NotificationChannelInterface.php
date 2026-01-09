<?php

namespace App\Services\Notifications;

use App\Models\Patient;

interface NotificationChannelInterface
{
    public function sendPatientRegistrationConfirmation(Patient $patient): void;
}
