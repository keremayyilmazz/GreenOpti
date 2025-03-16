<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CalculationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FactoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/route-details/{source}/{destination}/{type}', [CalculationController::class, 'getRouteDetails'])
    ->name('route.details');
Route::get('/', function () {
    return view('welcome');
});

// Kimlik doğrulama gerektiren route'lar
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Fabrika işlemleri
    Route::resource('factories', FactoryController::class);
    
    // Hesaplama işlemleri
    Route::get('/calculations', [CalculationController::class, 'index'])->name('calculations.index');
    Route::post('/calculations', [CalculationController::class, 'calculate'])->name('calculations.calculate');
    
    // Profil işlemleri
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth route'ları
require __DIR__.'/auth.php';