<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        Passport::$registersRoutes = false;

        Route::group([
            'as' => 'passport.',
            'middleware' => ['universal', InitializeTenancyByDomain::class],
            'prefix' => config('passport.path', 'oauth'),
            'namespace' => 'Laravel\Passport\Http\Controllers',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../../vendor/laravel/passport/src/../routes/web.php');
        });
    }

    public function boot()
    {
        Schema::defaultStringLength(191);

        Passport::loadKeysFrom(base_path(config('passport.key_path')));
    }
}
