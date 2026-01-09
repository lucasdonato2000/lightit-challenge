<?php

namespace App\Providers;

use App\Repositories\EloquentPatientRepository;
use App\Repositories\PatientRepositoryInterface;
use App\Services\Notifications\EmailNotificationChannel;
use App\Services\Notifications\NotificationService;
use App\Services\Notifications\NotificationServiceInterface;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
 
    public function register(): void
    {
        $this->app->bind(
            PatientRepositoryInterface::class,
            EloquentPatientRepository::class
        );

        $this->app->bind(
            NotificationServiceInterface::class,
            function ($app) {
           
                return new NotificationService(
                    new EmailNotificationChannel(),
                );
            }
        );
    }

    public function boot(): void
    {
        //
    }
}
