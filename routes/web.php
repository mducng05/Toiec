<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\User\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// ── Guest Routes ──────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// ── Logout ────────────────────────────────────────────
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Authenticated Routes ──────────────────────────────
Route::middleware('auth')->group(function () {

    // User routes
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Admin routes
    Route::prefix('admin')
        ->middleware('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

            // Phase 2: Exam CRUD & Management
            Route::resource('exams', ExamController::class);
            Route::post('exams/{exam}/toggle-publish', [ExamController::class, 'togglePublish'])->name('exams.toggle-publish');
            Route::post('exams/{exam}/add-part', [ExamController::class, 'addPart'])->name('exams.add-part');

            // Phase 2: Uploads Management
            Route::get('exams/{exam}/uploads', [UploadController::class, 'index'])->name('exams.uploads');
            Route::post('exams/{exam}/uploads/exam-pdf', [UploadController::class, 'uploadExamPdf'])->name('exams.uploads.exam-pdf');
            Route::post('exams/{exam}/uploads/answer-pdf', [UploadController::class, 'uploadAnswerPdf'])->name('exams.uploads.answer-pdf');
            Route::post('parts/{part}/uploads/audio', [UploadController::class, 'uploadAudio'])->name('parts.uploads.audio');
            Route::post('parts/{part}/audio/reorder', [UploadController::class, 'reorderAudio'])->name('parts.audio.reorder');
            Route::delete('assets/{asset}', [UploadController::class, 'deleteAsset'])->name('assets.destroy');
            Route::delete('audio/{audio}', [UploadController::class, 'deleteAudio'])->name('audio.destroy');
            Route::get('files/{path}', [UploadController::class, 'serveFile'])->name('files.serve');
        });
});
