<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\KycSendController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/dashboard/actualizar-estados', [App\Http\Controllers\DashboardController::class, 'actualizarEstados'])->middleware(['auth', 'verified'])->name('dashboard.actualizar-estados');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clientes routes
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');

    // KYC Send routes
    Route::prefix('kyc-send')->name('kyc-send.')->group(function () {
        Route::get('/create', [KycSendController::class, 'create'])->name('create');
        Route::post('/', [KycSendController::class, 'store'])->name('store');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\AdminController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\AdminController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [App\Http\Controllers\AdminController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [App\Http\Controllers\AdminController::class, 'update'])->name('update');
        Route::delete('/{user}', [App\Http\Controllers\AdminController::class, 'destroy'])->name('destroy');
    });

    // Employees routes
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::patch('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
    });
});


require __DIR__.'/auth.php';
