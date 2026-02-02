<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\DurationController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [IndexController::class, 'loginPage'])->name('login-page');

Route::get('/admin/login', [IndexController::class, 'loginPage'])->name('login-admin');

Route::post('admin-login', [AccessController::class, 'adminLogin'])->name('admin-login');

Route::get('/logout', [AccessController::class, 'Logout'])->name('logout');

Route::group(['middleware' => ['prevent-back-history', 'admin_auth']], function () {
    // admin dashboard
    Route::get('/dashboard', [DashboardController::class, 'Dashboard'])->name('dashboard');
    Route::get('password-change', [AccessController::class, 'passwordChange'])->name('password-change');
    Route::post('change-password', [AccessController::class, 'changePassword'])->name('change-password');

    // Service Routes
    Route::resource('services', ServiceController::class);
    Route::post('service-status-update', [ServiceController::class, 'serviceStatusUpdate'])->name('service-status-update');

    Route::resource('durations', DurationController::class);
    Route::post('duration-status-update', [DurationController::class, 'durationStatusUpdate'])->name('duration-status-update');

    Route::resource('branches', BranchController::class);
    Route::post('branch-status-update', [BranchController::class, 'branchStatusUpdate'])->name('branch-status-update');
});


Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return 'All caches (config, route, view, application) have been cleared!';
});
