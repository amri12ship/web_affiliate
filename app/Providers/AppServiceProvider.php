<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer(
            ['layouts.public', 'admin.layouts.app', 'admin.auth.login'],
            fn ($view) => $view->with('siteName', Setting::value('site_name', config('app.name', 'Laravel'))),
        );
    }
}
