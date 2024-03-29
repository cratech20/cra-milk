<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CowController;
use App\Http\Controllers\CowGroupController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/json/device-messages', [HomeController::class, 'deviceMessages']);
Route::get('/json/table', [HomeController::class, 'table']);

Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        $user = auth()->user();
        if ($user->hasRole('client')) {
            return view('cabinet.client');
        } else if ($user->hasRole('employee')) {
            return view('cabinet.admin');
        }
    });

    Route::get('/test', [HomeController::class, 'testJSON']);

    // Device

    Route::resource('devices', DeviceController::class);

    Route::get('/devices/destroy/{device}', [DeviceController::class, 'destroy'])
        ->name('devices.destroy');

    Route::post('/devices/clients/change', [DeviceController::class, 'clientChange'])
        ->name('devices.clients.change');

    Route::get('/devices/client/summary/{client?}', [DeviceController::class, 'summaryTable'])
        ->name('devices.summary_table');

    Route::prefix('devices')->group(function () {

        Route::get('{device}/messages', [MessageController::class, 'show'])
            ->name('devices.messages');

    });

    Route::prefix('reports')->group(function () {

        Route::get('/', [ReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/liters', [ReportController::class, 'liters'])
            ->name('reports.liters');

        Route::get('/liters-hour', [ReportController::class, 'litersByHour'])
            ->name('reports.liters.hour');

        Route::get('/liters-hour2', [ReportController::class, 'litersByHour2'])
            ->name('reports.liters.hour2');

        Route::get('/liters-device', [ReportController::class, 'litersByDevice'])
            ->name('reports.liters.device');

        Route::get('/impulse', [ReportController::class, 'impulse'])
            ->name('reports.impulse');

        Route::get('/impulse-device', [ReportController::class, 'impulseByDevice'])
            ->name('reports.impulse.device');

        Route::get('/mlk', [ReportController::class, 'mlk'])
            ->name('reports.mlk');
    });

    Route::prefix('users')->group(function () {

        Route::get('/', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/change/password/{user}', [UserController::class, 'changePasswordForm'])
            ->name('users.change.password');

        Route::post('/change/password/{user}', [UserController::class, 'changePassword'])
            ->name('users.change.password.save');

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

        Route::post('divisions', [DivisionController::class, 'store'])
            ->name('clients.divisions.store');

        Route::get('{client}/farms', [FarmController::class, 'index'])
            ->name('clients.farms.index');

        Route::post('farms', [FarmController::class, 'store'])
            ->name('clients.farms.store');

        Route::get('/cows/linking', [CowController::class, 'linking'])
            ->name('clients.cows.linking');

        Route::get('{client}/cows', [CowController::class, 'index'])
            ->name('clients.cows.index');

        Route::get('/cows/{id}', [CowController::class, 'show'])
            ->name('clients.cows.edit');

        Route::patch('/cows/{id}', [CowController::class, 'update'])
            ->name('clients.cows.edit');

        Route::get('{client}/cows/groups', [CowGroupController::class, 'index'])
            ->name('clients.cows.groups.index');

        Route::post('cows/groups', [CowGroupController::class, 'store'])
            ->name('clients.cows.groups.store');

        Route::post('cows/groups/change', [CowGroupController::class, 'change'])
            ->name('clients.cows.groups.change');
    });
});

Auth::routes(['register' => false]);
