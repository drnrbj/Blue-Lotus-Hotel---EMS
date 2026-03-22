<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\TrainingController;

// ── Root ────────────────────────────────────────────────────
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// ── Auth ─────────────────────────────────────────────────────
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// ── Protected ────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'totalEmployees' => 248,
        ]);
    })->name('dashboard');

    // ── Employee Management ──────────────────────────────────
    // IMPORTANT: static paths (new-hires) must come BEFORE the resource routes
    // to prevent Laravel matching "new-hires" as an {employee} wildcard.
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/new-hires',                                   [EmployeeController::class, 'newHires'])     ->name('new-hires');
        Route::post('/new-hires/{applicant}/create-profile',       [EmployeeController::class, 'createProfile'])->name('create-profile');
        Route::post('/{employee}/terminate',                       [EmployeeController::class, 'terminate'])   ->name('terminate');
    });

    // Resource routes (index, create, store, show, edit, update, destroy)
    Route::resource('employees', EmployeeController::class);

    // ── Attendance / Timekeeping ─────────────────────────────
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/',                          [AttendanceController::class, 'index'])         ->name('index');
        Route::post('/fetch',                    [AttendanceController::class, 'fetch'])         ->name('fetch');
        Route::get('/schedules',                 [AttendanceController::class, 'schedules'])     ->name('schedules');
        Route::post('/schedules',                [AttendanceController::class, 'storeSchedule']) ->name('schedules.store');
        Route::put('/schedules/{schedule}',      [AttendanceController::class, 'updateSchedule'])->name('schedules.update');
        Route::get('/leaves',                    [AttendanceController::class, 'leaves'])        ->name('leaves');
        Route::post('/leaves',                   [AttendanceController::class, 'storeLeave'])    ->name('leaves.store');
        Route::post('/leaves/{leave}/approve',   [AttendanceController::class, 'approveLeave']) ->name('leaves.approve');
        Route::post('/leaves/{leave}/reject',    [AttendanceController::class, 'rejectLeave'])  ->name('leaves.reject');
    });

    // ── Placeholders (swap for real controllers later) ───────
    Route::view('/payroll',     'payroll.index')    ->name('payroll.index');
    // ── Performance Evaluation ───────────────────────────────
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/',                          [EvaluationController::class, 'index'])    ->name('index');
        Route::post('/',                         [EvaluationController::class, 'store'])    ->name('store');
        Route::post('/{evaluation}/score',       [EvaluationController::class, 'score'])   ->name('score');
        Route::delete('/{evaluation}',           [EvaluationController::class, 'destroy']) ->name('destroy');
        Route::get('/analytics',                 [EvaluationController::class, 'analytics'])->name('analytics');
    });
    // ── Recruitment ──────────────────────────────────────────
    Route::prefix('recruitment')->name('recruitment.')->group(function () {
        Route::get('/',                                    [RecruitmentController::class, 'index'])                ->name('index');
        Route::post('/postings',                           [RecruitmentController::class, 'storePosting'])         ->name('postings.store');
        Route::put('/postings/{posting}',                  [RecruitmentController::class, 'updatePosting'])        ->name('postings.update');
        Route::delete('/postings/{posting}',               [RecruitmentController::class, 'destroyPosting'])       ->name('postings.destroy');
        Route::get('/applicants',                          [RecruitmentController::class, 'applicants'])           ->name('applicants');
        Route::post('/applicants',                         [RecruitmentController::class, 'storeApplicant'])       ->name('applicants.store');
        Route::post('/applicants/{applicant}/status',      [RecruitmentController::class, 'updateApplicantStatus'])->name('applicants.status');
        Route::delete('/applicants/{applicant}',           [RecruitmentController::class, 'destroyApplicant'])     ->name('applicants.destroy');
        Route::get('/interviews',                          [RecruitmentController::class, 'interviews'])           ->name('interviews');
        Route::post('/interviews',                         [RecruitmentController::class, 'storeInterview'])       ->name('interviews.store');
        Route::post('/interviews/{interview}/status',      [RecruitmentController::class, 'updateInterviewStatus'])->name('interviews.status');
    });

    // ── Training ─────────────────────────────────────────────
    Route::prefix('training')->name('training.')->group(function () {
        Route::get('/',                                    [TrainingController::class, 'index'])           ->name('index');
        Route::post('/',                                   [TrainingController::class, 'store'])           ->name('store');
        Route::put('/{program}',                           [TrainingController::class, 'update'])          ->name('update');
        Route::delete('/{program}',                        [TrainingController::class, 'destroy'])         ->name('destroy');
        Route::get('/participants',                        [TrainingController::class, 'participants'])    ->name('participants');
        Route::post('/enroll',                             [TrainingController::class, 'enroll'])          ->name('enroll');
        Route::post('/participants/{participant}/complete',[TrainingController::class, 'markComplete'])    ->name('participants.complete');
        Route::delete('/participants/{participant}',       [TrainingController::class, 'removeParticipant'])->name('participants.remove');
    });

});