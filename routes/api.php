<?php

use App\Http\Controllers\DoorOpenerController;
use Illuminate\Support\Facades\Route;

Route::get('door-opener/poll', [DoorOpenerController::class, 'poll'])
    ->name('api.door-opener.poll');

Route::post('door-opener/doorbell', [DoorOpenerController::class, 'doorbell'])
    ->name('api.door-opener.doorbell');
