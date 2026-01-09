<?php

namespace App\Services\Notifications;

use App\Models\Patient;
use Illuminate\Support\Facades\Log;

class NotificationService implements NotificationServiceInterface
{
    private array $channels;

    public function __construct(NotificationChannelInterface ...$channels)
    {
        $this->channels = $channels;
    }

    public function sendPatientRegistrationConfirmation(Patient $patient): void
    {
        foreach ($this->channels as $channel) {
            try {
                $channel->sendPatientRegistrationConfirmation($patient);
            } catch (\Exception $e) {
                Log::error('Failed to send notification via ' . get_class($channel), [
                    'patient_id' => $patient->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function addChannel(NotificationChannelInterface $channel): void
    {
        $this->channels[] = $channel;
    }
}
