<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        $user = auth()->user();
        if ($user->hasRole('client')) {
            return view('clients.index');
        } else if ($user->hasRole('employee')) {
            return redirect()->route('devices.index');
        } else {

        }
    });

    Route::get('/test', [\App\Http\Controllers\HomeController::class, 'testJSON']);

    // Device

    Route::resource('devices', DeviceController::class);

    Route::get('/devices/destroy/{device}', [DeviceController::class, 'destroy'])
        ->name('devices.destroy');

    Route::prefix('devices')->group(function () {

        Route::get('/messages/{device}', [MessageController::class, 'show'])
            ->name('devices.messages');

    });

    Route::prefix('reports')->group(function () {

        Route::get('/', [ReportController::class, 'index'])
            ->name('reports.index');
    });

    Route::prefix('users')->group(function () {

        Route::get('/', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/create/inn', [UserController::class, 'inn'])
            ->name('users.registration.inn');

        Route::get('/create/form', [UserController::class, 'create'])
            ->name('users.registration.create');

        Route::post('/', [UserController::class, 'store'])
            ->name('users.store');

        Route::get('/roles', [UserRoleController::class, 'index'])
            ->name('users.roles.index');

        Route::post('/roles', [UserRoleController::class, 'update'])
            ->name('users.roles.update');

    });

});

Auth::routes(['register' => false]);
