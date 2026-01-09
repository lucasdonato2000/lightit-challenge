<?php

namespace App\Jobs;

use App\Mail\PatientWelcomeMail;
use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPatientWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;
    public int $timeout = 30;

    public function __construct(
        public Patient $patient
    ) {
    }

    public function handle(): void
    {
        try {
            Log::info('Sending welcome email to patient', [
                'patient_id' => $this->patient->id,
                'email' => $this->patient->email,
            ]);

            Mail::to($this->patient->email)
                ->send(new PatientWelcomeMail($this->patient));

            Log::info('Welcome email sent successfully', [
                'patient_id' => $this->patient->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'patient_id' => $this->patient->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Welcome email job failed after all retries', [
            'patient_id' => $this->patient->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
