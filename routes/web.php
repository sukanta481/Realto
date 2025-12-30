<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Real Estate CRM - All routes handled by Vue SPA
|
*/

// Serve the Vue SPA for all routes
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
