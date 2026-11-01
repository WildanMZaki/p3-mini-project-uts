<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportEvidenceController;
use App\Http\Controllers\ReviewController;
use App\Models\Report;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reports Menu
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])
        ->can('create', Report::class)
        ->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])
        ->can('create', Report::class)
        ->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])
        ->can('view', 'report')
        ->name('reports.show');
    Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])
        ->can('update', 'report')
        ->name('reports.edit');
    Route::put('/reports/{report}', [ReportController::class, 'update'])
        ->can('update', 'report')
        ->name('reports.update');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])
        ->can('delete', 'report')
        ->name('reports.destroy');
    Route::get('/reports/{report}/evidence', ReportEvidenceController::class)
        ->can('view', 'report')
        ->name('reports.evidence');

    // Peninjauan oleh relawan pemantau
    Route::post('/reports/{report}/claim', [ReviewController::class, 'claim'])
        ->can('review', 'report')
        ->name('reports.claim');
    Route::post('/reports/{report}/review', [ReviewController::class, 'decide'])
        ->can('review', 'report')
        ->name('reports.review');
});
