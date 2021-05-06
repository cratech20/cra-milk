<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\FarmController;
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
            return view('cabinet.client');
        } else if ($user->hasRole('employee')) {
            return view('cabinet.admin');
        } else {

        }
    });

    Route::get('/test', [\App\Http\Controllers\HomeController::class, 'testJSON']);

    // Device

    Route::resource('devices', DeviceController::class);

    Route::get('/devices/client/{client?}', [DeviceController::class, 'index'])
        ->name('devices.index');

    Route::get('/devices/destroy/{device}', [DeviceController::class, 'destroy'])
        ->name('devices.destroy');

    Route::get('/devices/client/{client?}/summary', [DeviceController::class, 'summaryTable'])
        ->name('devices.summary_table');

    Route::prefix('devices')->group(function () {

        Route::get('{device}/messages', [MessageController::class, 'show'])
            ->name('devices.messages');

    });

    Route::prefix('reports')->group(function () {

        Route::get('/', [ReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/bi', [ReportController::class, 'bi'])
            ->name('reports.bi');
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

    Route::prefix('clients')->group(function () {

        Route::get('/', [ClientController::class, 'index'])
            ->name('clients.index');

        Route::get('{client}/divisions', [DivisionController::class, 'index'])
            ->name('clients.divisions.index');

        Route::get('{client}/divisions/{division}', [FarmController::class, 'index'])
            ->name('clients.farm.index');
    });
});

Auth::routes(['register' => false]);
