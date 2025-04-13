<?php

use App\Http\Controllers\CentralLoginController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantPublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return 'API ESCUELA PRESENTE';
});

Route::post('login', [CentralLoginController::class, 'login']);
Route::get('tenants/public', [TenantPublicController::class, 'index']);
Route::post('tenants/public', [TenantPublicController::class, 'store']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [CentralLoginController::class, 'logout']);
    Route::get('me', [CentralLoginController::class, 'me']);

    Route::get('tenants', [TenantController::class, 'index']);
    Route::post('tenants', [TenantController::class, 'store']);
    Route::post('tenants/{id}', [TenantController::class, 'destroy']);
});
