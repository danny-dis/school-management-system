<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Facades\ModuleManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register module blade directives
        Blade::directive('module', function ($module) {
            return "<?php if(\\App\\Facades\\ModuleManager::isModuleEnabled({$module})) : ?>";
        });
        
        Blade::directive('endmodule', function () {
            return "<?php endif; ?>";
        });
        
        Blade::directive('notmodule', function ($module) {
            return "<?php if(!\\App\\Facades\\ModuleManager::isModuleEnabled({$module})) : ?>";
        });
        
        Blade::directive('endnotmodule', function () {
            return "<?php endif; ?>";
        });
        
        // Register module gates
        $this->registerModuleGates();
    }
    
    /**
     * Register module gates.
     *
     * @return void
     */
    protected function registerModuleGates()
    {
        Gate::define('access-module', function ($user, $moduleKey) {
            // Super admins can access all modules
            if ($user->is_super_admin) {
                return true;
            }
            
            // Check if the module is enabled
            return ModuleManager::isModuleEnabled($moduleKey);
        });
    }
}
