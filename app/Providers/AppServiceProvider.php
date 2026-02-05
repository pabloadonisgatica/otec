<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\AppSetting;
use Illuminate\Support\Facades\View;


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
        View::share('appLogo', AppSetting::get('app_logo'));
    }

}
