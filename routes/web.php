<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromptController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/prompt', [PromptController::class, 'index'])->name('prompt');
    Route::get('/prompt/{prompt_chain_id}', [PromptController::class, 'continue'])->name('prompt.continue');
    Route::put('/prompt/{prompt_chain_id}', [PromptController::class, 'newMessage'])->name('prompt.new-message');
    Route::post('/prompt', [PromptController::class, 'start'])->name('prompt.start');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
