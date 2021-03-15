<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return redirect()->route('devices.index');
    });

    // Devices

    Route::resource('devices', DeviceController::class);

    Route::get('/devices/destroy/{device}', [DeviceController::class, 'destroy'])
        ->name('devices.destroy');

    Route::prefix('devices')->group(function () {

        Route::get('/messages/{device}', [MessageController::class, 'show'])
            ->name('devices.messages');

    });


});

Auth::routes(['register' => false]);
