<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EmployeeController;

// public login form
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// handle login submit (example with manual auth)
Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email'    => ['required','email'],
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

// logout
Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// dashboard page (protected)
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        // provide data to view here (or use controller)
        return view('dashboard', [
            'totalEmployees' => 248,
            // ... etc
        ]);
    })->name('dashboard');

    // optional /app route if you have one
    Route::view('/app', 'app')->name('app');
});

Route::middleware('auth')->group(function () {
    Route::resource('employees', App\Http\Controllers\EmployeeController::class);
    Route::resource('attendance', App\Http\Controllers\AttendanceController::class);
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
 
// Employee Management
Route::prefix('employees')->name('employees.')->group(function () {
 
    Route::get('/',                                   [EmployeeController::class, 'index'])         ->name('index');
    Route::put('/{employee}',                         [EmployeeController::class, 'update'])        ->name('update');
    Route::post('/{employee}/terminate',              [EmployeeController::class, 'terminate'])     ->name('terminate');
 
    // New Hires
    Route::get('/new-hires',                          [EmployeeController::class, 'newHires'])      ->name('new-hires');
    Route::post('/new-hires/{applicant}/create-profile', [EmployeeController::class, 'createProfile'])->name('create-profile');
 
});
 