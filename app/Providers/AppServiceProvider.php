<?php

namespace App\Providers;

use App\Observers\UserObserver;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Set default string length for MySQL older versions
        Schema::defaultStringLength(191);

        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // User caching observer
        User::observe(UserObserver::class);

        // Use Bootstrap pagination
        Paginator::useBootstrap();

        // Enable strict mode for models in development
        if (config('app.env') === 'local' || config('app.env') === 'testing') {
            Model::shouldBeStrict();
        }

        // Custom if query builder macro
        Builder::macro('if', function ($condition, $column, $operator, $value) {
            if ($condition) {
                return $this->where($column, $operator, $value);
            }

            return $this;
        });

        // Custom Blade directives
        Blade::directive('money', function ($amount) {
            return "<?php echo config('app.currency_symbol') . number_format($amount, 2); ?>";
        });

        Blade::directive('date', function ($expression) {
            return "<?php echo ($expression)->format('M d, Y'); ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register singleton services
        $this->app->singleton('module.manager', function ($app) {
            return new \App\Services\ModuleManager();
        });

        // Register repository bindings
        $this->app->bind(\App\Repositories\Contracts\UserRepositoryInterface::class, \App\Repositories\UserRepository::class);
        $this->app->bind(\App\Repositories\Contracts\StudentRepositoryInterface::class, \App\Repositories\StudentRepository::class);
        $this->app->bind(\App\Repositories\Contracts\TeacherRepositoryInterface::class, \App\Repositories\TeacherRepository::class);
    }
}
