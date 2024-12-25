<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Voter\VotingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//    Route::post('/email-verify', [ProfileController::class, 'getVerify'])->name('verification.send');

    // Common authenticated routes
    Route::get('/dashboard', [VotingController::class, 'index'])->name('dashboard');

    Route::middleware('AdminMiddleware')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/create', [AdminController::class, 'create'])->name('admin.create');
        Route::post('/admin', [AdminController::class, 'store'])->name('admin.store');
        Route::get('/admin/{candidate}/edit', [AdminController::class, 'edit'])->name('admin.edit');
        Route::put('/admin/{candidate}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/admin/{candidate}', [AdminController::class, 'destroy'])->name('admin.destroy');
        Route::patch('/admin/{candidate}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.toggle-status');
        Route::get('/admin/export', [AdminController::class, 'exportCandidates'])->name('admin.export');
    });

    Route::middleware('VoterMiddleware')->group(function () {
        Route::post('/vote/{candidate}', [VotingController::class, 'vote'])->name('voting.vote');

    });

});

require __DIR__.'/auth.php';


