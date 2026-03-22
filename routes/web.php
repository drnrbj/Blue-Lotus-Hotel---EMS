<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EmployeeController;

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

    // ── Attendance ───────────────────────────────────────────
    Route::resource('attendance', App\Http\Controllers\AttendanceController::class);

    // ── Placeholders (swap for real controllers later) ───────
    Route::view('/payroll',     'payroll.index')    ->name('payroll.index');
    Route::view('/performance', 'performance.index')->name('performance.index');
    Route::view('/recruitment', 'recruitment.index')->name('recruitment.index');

});