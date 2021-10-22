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
use App\Http\Controllers\GateController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/json/device-messages', [HomeController::class, 'deviceMessages']);

Route::middleware(['auth', 'isblock'])->group(function () {

    Route::get('/', [HomeController::class, 'index']);

    Route::get('/test', [HomeController::class, 'testJSON']);
    Route::get('/run', [HomeController::class, 'run']);
    Route::get('/get-data', [HomeController::class, 'getData']);
    Route::get('/get-dates', [HomeController::class, 'getDates']);
    Route::post('/get-mac', [HomeController::class, 'getMac']);
    Route::post('/get-chart-data', [HomeController::class, 'getChartData']);

    // Device

    // Route::resource('devices', DeviceController::class);

    Route::get('/devices/destroy/{device}', [DeviceController::class, 'destroy'])
        ->name('devices.destroy');

    Route::post('/devices/clients/change', [DeviceController::class, 'clientChange'])
        ->name('devices.clients.change');

    Route::get('/devices/client/summary/{client?}', [DeviceController::class, 'summaryTable'])
        ->name('devices.summary_table');

    Route::prefix('devices')->group(function () {
        Route::get('/', [HomeController::class, 'index']);
        Route::get('/get-all', [DeviceController::class, 'getAllDevices']);
        Route::post('/save', [DeviceController::class, 'store']);
        Route::post('/update', [DeviceController::class, 'update']);
        Route::post('/command', [DeviceController::class, 'command']);
        Route::post('/migrate', [DeviceController::class, 'migrate']);
        Route::get('/get-gates', [GateController::class, 'getGate']);
        Route::post('/detach', [DeviceController::class, 'detach']);
        Route::get('{client}/get-empty-device', [DeviceController::class, 'getEmptyDevice']);

        Route::get('{id}/messages', [MessageController::class, 'show'])
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
        Route::get('/', [HomeController::class, 'index']);
        Route::get('/getall', [UserController::class, 'getAllUsers']);
        Route::get('/delete/{id}', [UserController::class, 'delUser']);
        Route::post('/block', [UserController::class, 'blockUser']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::post('/create', [UserController::class, 'store']);
        Route::get('/roles', [UserRoleController::class, 'index']);
        Route::post('/create/form', [UserController::class, 'create']);

        Route::get('/change/password/{user}', [UserController::class, 'changePasswordForm'])
            ->name('users.change.password');

        Route::get('/create/inn', [UserController::class, 'inn'])
            ->name('users.registration.inn');

        Route::post('/', [UserController::class, 'store'])
            ->name('users.store');

        Route::post('/roles', [UserRoleController::class, 'update'])
            ->name('users.roles.update');

    });

    Route::prefix('clients')->group(function () {
        Route::get('/', [HomeController::class, 'index']);
        Route::get('/get-all', [ClientController::class, 'index']);
        Route::get('{client}/divisions', [DivisionController::class, 'index']);
        Route::post('divisions/save', [DivisionController::class, 'store']);
        Route::post('divisions/del', [DivisionController::class, 'delete']);
        Route::get('{client}/farms', [FarmController::class, 'index']);
        Route::post('farms/save', [FarmController::class, 'store']);
        Route::post('farms/del', [FarmController::class, 'delete']);
        Route::get('{client}/cows/groups', [CowGroupController::class, 'index']);
        Route::post('cows/groups/save', [CowGroupController::class, 'store']);
        Route::post('cows/groups/del', [CowGroupController::class, 'delete']);
        Route::get('/cows/linking', [CowController::class, 'linking']);
        Route::get('/get/{id}', [ClientController::class, 'getClientById']);
        Route::get('{client}/cows', [HomeController::class, 'index']);
        Route::get('{id}/get-cows', [CowController::class, 'index']);
        Route::post('/cows/edit', [CowController::class, 'update']);
        Route::get('{id}/devices', [HomeController::class, 'index']);
        Route::get('{id}/all-devices', [DeviceController::class, 'summaryTable']);

        Route::get('/cows/{id}', [CowController::class, 'show'])
            ->name('clients.cows.edit');









        Route::post('cows/groups/change', [CowGroupController::class, 'change'])
            ->name('clients.cows.groups.change');
    });
});

Auth::routes(['register' => false]);
