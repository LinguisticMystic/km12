<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DoorOpenerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('wayfinder', 'tools.wayfinder')->name('wayfinder');
Route::view('calendar', 'tools.calendar')->name('calendar');
Route::view('about', 'about')->name('about');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::view('door-opener', 'tools.door-opener')->name('door-opener');
    Route::post('door-opener/door', [DoorOpenerController::class, 'openDoor'])->name('door-opener.open-door');
    Route::post('door-opener/gate', [DoorOpenerController::class, 'openGate'])->name('door-opener.open-gate');
});
