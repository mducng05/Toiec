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

// User public exam show
Route::get('/exams/{exam}', [\App\Http\Controllers\User\ExamController::class, 'show'])->name('exams.show');

// ── Authenticated Routes ──────────────────────────────
Route::middleware('auth')->group(function () {

    // User Exam Taking & Results
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/exams/{exam}/start', [\App\Http\Controllers\User\ExamController::class, 'start'])->name('exams.start');
    Route::get('/attempts/{attempt}/take', [\App\Http\Controllers\User\ExamController::class, 'take'])->name('exams.take');
    Route::post('/attempts/{attempt}/answers', [\App\Http\Controllers\User\ExamController::class, 'saveAnswer'])->name('exams.save-answer');
    Route::post('/attempts/{attempt}/submit', [\App\Http\Controllers\User\ExamController::class, 'submit'])->name('exams.submit');
    Route::get('/attempts/{attempt}/results', [\App\Http\Controllers\User\ExamController::class, 'result'])->name('exams.results');
    Route::get('/attempts/{attempt}/review', [\App\Http\Controllers\User\ExamController::class, 'review'])->name('exams.review');

    // My Exams & User Upload Workspace
    Route::get('/my-exams', [\App\Http\Controllers\User\MyExamController::class, 'index'])->name('my-exams.index');
    Route::get('/my-exams/create', [\App\Http\Controllers\User\MyExamController::class, 'create'])->name('my-exams.create');
    Route::post('/my-exams', [\App\Http\Controllers\User\MyExamController::class, 'store'])->name('my-exams.store');
    Route::get('/my-exams/{exam}', [\App\Http\Controllers\User\MyExamController::class, 'show'])->name('my-exams.show');
    Route::get('/my-exams/{exam}/edit', [\App\Http\Controllers\User\MyExamController::class, 'edit'])->name('my-exams.edit');
    Route::put('/my-exams/{exam}', [\App\Http\Controllers\User\MyExamController::class, 'update'])->name('my-exams.update');
    Route::delete('/my-exams/{exam}', [\App\Http\Controllers\User\MyExamController::class, 'destroy'])->name('my-exams.destroy');
    Route::get('/my-exams/{exam}/uploads', [\App\Http\Controllers\User\MyExamController::class, 'uploads'])->name('my-exams.uploads');
    Route::post('/my-exams/{exam}/uploads/exam-pdf', [\App\Http\Controllers\User\MyExamController::class, 'uploadExamPdf'])->name('my-exams.uploads.exam-pdf');
    Route::post('/my-exams/{exam}/uploads/answer-pdf', [\App\Http\Controllers\User\MyExamController::class, 'uploadAnswerPdf'])->name('my-exams.uploads.answer-pdf');
    Route::post('/my-parts/{part}/uploads/audio', [\App\Http\Controllers\User\MyExamController::class, 'uploadAudio'])->name('my-parts.uploads.audio');
    Route::get('/my-exams/{exam}/parser', [\App\Http\Controllers\User\MyExamController::class, 'parser'])->name('my-exams.parser');
    Route::post('/my-exams/{exam}/parser/answers', [\App\Http\Controllers\User\MyExamController::class, 'parseAnswers'])->name('my-exams.parser.answers');
    Route::post('/my-exams/{exam}/parser/questions', [\App\Http\Controllers\User\MyExamController::class, 'parseQuestions'])->name('my-exams.parser.questions');

    // User Test History
    Route::get('/history', [\App\Http\Controllers\User\HistoryController::class, 'index'])->name('history.index');

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

            // Phase 3: Question & Passage Management
            Route::get('exams/{exam}/questions', [\App\Http\Controllers\Admin\QuestionController::class, 'index'])->name('exams.questions.index');
            Route::get('parts/{part}/questions/create', [\App\Http\Controllers\Admin\QuestionController::class, 'create'])->name('parts.questions.create');
            Route::post('parts/{part}/questions', [\App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('parts.questions.store');
            Route::get('questions/{question}/edit', [\App\Http\Controllers\Admin\QuestionController::class, 'edit'])->name('questions.edit');
            Route::put('questions/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'update'])->name('questions.update');
            Route::delete('questions/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('questions.destroy');
            Route::post('questions/{question}/quick-answer', [\App\Http\Controllers\Admin\QuestionController::class, 'quickAnswer'])->name('questions.quick-answer');
            Route::post('parts/{part}/questions/generate-slots', [\App\Http\Controllers\Admin\QuestionController::class, 'generateSlots'])->name('parts.questions.generate-slots');

            Route::get('parts/{part}/passages/create', [\App\Http\Controllers\Admin\PassageController::class, 'create'])->name('parts.passages.create');
            Route::post('parts/{part}/passages', [\App\Http\Controllers\Admin\PassageController::class, 'store'])->name('parts.passages.store');
            Route::get('passages/{passage}/edit', [\App\Http\Controllers\Admin\PassageController::class, 'edit'])->name('passages.edit');
            Route::put('passages/{passage}', [\App\Http\Controllers\Admin\PassageController::class, 'update'])->name('passages.update');
            Route::delete('passages/{passage}', [\App\Http\Controllers\Admin\PassageController::class, 'destroy'])->name('passages.destroy');

            // Phase 5: Document Automation & Parser
            Route::get('exams/{exam}/parser', [\App\Http\Controllers\Admin\ParserController::class, 'index'])->name('exams.parser.index');
            Route::post('exams/{exam}/parser/answers', [\App\Http\Controllers\Admin\ParserController::class, 'parseAnswers'])->name('exams.parser.answers');
            Route::post('exams/{exam}/parser/questions', [\App\Http\Controllers\Admin\ParserController::class, 'parseQuestions'])->name('exams.parser.questions');
        });
});
