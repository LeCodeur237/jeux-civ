<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Models\Gift;

Route::post('/logout', [AuthController::class, 'logout']);

// Routes accessibles uniquement aux invités (non connectés)
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('grattage.welcomex');
    })->name('welcome');

    Route::get('/register', function () {
        return view('roulette.register');
    });

    Route::get('/valentines-day', function () {
        return view('grattage.homex');
    });

    Route::get('/registering', function () {
        return view('grattage.registering');
    });

    Route::get('/login-form', function () {
        return view('roulette.login');
    })->name('login');

    Route::post('/register-control', [AuthController::class, 'registerControl']);
    Route::post('/login-control', [AuthController::class, 'loginControl']);
    Route::post('/register-player', [AuthController::class, 'registerPlayer']);
    Route::post('/save-player-game-result', [AuthController::class, 'savePlayerGameResult']);
    Route::post('/check-player-status', [AuthController::class, 'checkPlayerStatus']);
});

// Routes accessibles uniquement aux utilisateurs connectés
Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        $gifts = Gift::all();
        return view('roulette.home', compact('gifts'));
    })->name('home');

    Route::get('/success', [AuthController::class, 'successControl']);
    Route::post('/save-game-result', [AuthController::class, 'saveGameResult']);

    // Routes Administration
    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/users/export', [AdminController::class, 'exportUsersCsv'])->name('admin.users.export');
        Route::get('/gifts', [AdminController::class, 'gifts'])->name('admin.gifts');
        Route::post('/gifts', [AdminController::class, 'storeGift'])->name('admin.gifts.store');
        Route::delete('/gifts/{id}', [AdminController::class, 'deleteGift'])->name('admin.gifts.delete');
    });
});
