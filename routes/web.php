<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DoorOpenerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\LocaleController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'upcomingEvents' => Event::upcoming(),
    ]);
})->name('home');

Route::post('locale', [LocaleController::class, 'update'])->name('locale.update');

require __DIR__.'/wayfinder.php';

Route::view('calendar', 'tools.calendar')->name('calendar');
Route::view('about', 'about')->name('about');

Route::get('events', [EventController::class, 'index'])->name('events.index');
Route::get('events/{id}', [EventController::class, 'redirectFromId'])
    ->whereNumber('id');
Route::get('events/{event:slug}', [EventController::class, 'show'])->name('events.show');

Route::get('galleries', [GalleryController::class, 'index'])->name('galleries.index');
Route::get('galleries/{id}', [GalleryController::class, 'redirectFromId'])
    ->whereNumber('id');
Route::get('galleries/{gallery:slug}', [GalleryController::class, 'show'])->name('galleries.show');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    // TODO: Keep Door opener off the public home grid until Arduino runs km12_door_opener firmware (API). Hidden from welcome.blade.php for now.
    Route::view('door-opener', 'tools.door-opener')->name('door-opener');
    Route::post('door-opener/door', [DoorOpenerController::class, 'openDoor'])->name('door-opener.open-door');
    Route::post('door-opener/gate', [DoorOpenerController::class, 'openGate'])->name('door-opener.open-gate');
});
