<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\AppSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
    {
        // Evita romper comandos como `migrate` en una instalación
        // limpia, donde la tabla app_settings todavía no existe.
        $logo = null;

        if (Schema::hasTable('app_settings')) {
            $logo = AppSetting::get('app_logo');
        }

        View::share('appLogo', $logo);
    }

}