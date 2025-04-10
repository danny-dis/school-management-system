<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register repositories
        $this->app->bind(\App\Repositories\Contracts\UserRepositoryInterface::class, \App\Repositories\UserRepository::class);
        $this->app->bind(\App\Repositories\Contracts\StudentRepositoryInterface::class, \App\Repositories\StudentRepository::class);
        $this->app->bind(\App\Repositories\Contracts\TeacherRepositoryInterface::class, \App\Repositories\TeacherRepository::class);

        // Register services
        $this->app->singleton(\App\Services\NotificationService::class);
        $this->app->singleton(\App\Services\ModuleManager::class);
        $this->app->singleton(\App\Services\LicensingService::class);

        $this->app->singleton(\App\Services\UserService::class, function ($app) {
            return new \App\Services\UserService(
                $app->make(\App\Repositories\Contracts\UserRepositoryInterface::class)
            );
        });

        $this->app->singleton(\App\Services\StudentService::class, function ($app) {
            return new \App\Services\StudentService(
                $app->make(\App\Repositories\Contracts\StudentRepositoryInterface::class),
                $app->make(\App\Repositories\Contracts\UserRepositoryInterface::class)
            );
        });

        $this->app->singleton(\App\Services\TeacherService::class, function ($app) {
            return new \App\Services\TeacherService(
                $app->make(\App\Repositories\Contracts\TeacherRepositoryInterface::class),
                $app->make(\App\Repositories\Contracts\UserRepositoryInterface::class)
            );
        });

        $this->app->singleton(\App\Services\CommunicationService::class, function ($app) {
            return new \App\Services\CommunicationService(
                $app->make(\App\Services\NotificationService::class)
            );
        });

        $this->app->singleton(\App\Services\OnlineLearningService::class, function ($app) {
            return new \App\Services\OnlineLearningService(
                $app->make(\App\Services\NotificationService::class)
            );
        });

        $this->app->singleton(\App\Services\FeeManagementService::class, function ($app) {
            return new \App\Services\FeeManagementService(
                $app->make(\App\Services\NotificationService::class)
            );
        });

        $this->app->singleton(\App\Services\LibraryService::class, function ($app) {
            return new \App\Services\LibraryService(
                $app->make(\App\Services\NotificationService::class)
            );
        });

        $this->app->singleton(\App\Services\TimetableService::class, function ($app) {
            return new \App\Services\TimetableService(
                $app->make(\App\Services\NotificationService::class)
            );
        });

        $this->app->singleton(\App\Services\TransportationService::class, function ($app) {
            return new \App\Services\TransportationService(
                $app->make(\App\Services\NotificationService::class)
            );
        });

        $this->app->singleton(\App\Services\HealthRecordService::class, function ($app) {
            return new \App\Services\HealthRecordService(
                $app->make(\App\Services\NotificationService::class)
            );
        });

        $this->app->singleton(\App\Services\ReportingService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
