<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\CalculationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Factory routes
    Route::controller(FactoryController::class)->group(function () {
        Route::get('/factories', 'index')->name('factories.index');
        Route::post('/factories', 'store')->name('factories.store');
        Route::get('/factories/{factory}', 'show')->name('factories.show');
        Route::put('/factories/{factory}', 'update')->name('factories.update');
        Route::delete('/factories/{factory}', 'destroy')->name('factories.destroy');
        Route::get('/map-data', 'mapData')->name('factories.map-data');
        Route::patch('/factories/{factory}/location', 'updateLocation')->name('factories.update-location');
    });

    // Calculation routes
    Route::controller(CalculationController::class)->group(function () {
        Route::get('/calculations', 'index')->name('calculations');
        Route::post('/calculations', 'store');
        Route::get('/calculations/list', 'list');
    });
});

require __DIR__.'/auth.php';