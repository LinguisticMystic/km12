<?php

use App\Wayfinder\Http\Controllers\WayfinderController;
use Illuminate\Support\Facades\Route;

Route::get('wayfinder', WayfinderController::class)->name('wayfinder');
