<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EvaluationFormController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\NewHireController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveBalanceController;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ─── Protected ────────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/logout',          [AuthController::class, 'logout']);
        Route::get('/me',               [AuthController::class, 'me']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // ── Users (for interviewer dropdown etc.) ─────────────────────────────────
    Route::get('/users', function (\Illuminate\Http\Request $request) {
        $query = \App\Models\User::query();
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        return response()->json([
            'success' => true,
            'data'    => $query->get(['id', 'name', 'email', 'role']),
        ]);
    });

    // ── Employees — Admin (read only) + HR (full) ──────────────────
    Route::middleware('role:Admin,HR')->prefix('employees')->group(function () {
        // Read — both roles
        Route::get('/departments',    [EmployeeController::class, 'getDepartments']);
        Route::get('/job-categories', [EmployeeController::class, 'getJobCategories']);
        Route::get('/salary-mapping', [EmployeeController::class, 'getSalaryMapping']);
        Route::get('/archived',       [EmployeeController::class, 'archived']);
        Route::get('/',               [EmployeeController::class, 'index']);
        Route::get('/{employee}',     [EmployeeController::class, 'show']);
        Route::get('/{id}/export',    [EmployeeController::class, 'export']);
    });

    Route::middleware('role:HR')->prefix('employees')->group(function () {
        // Write — HR only
        Route::post('/',                   [EmployeeController::class, 'store']);
        Route::put('/{employee}',          [EmployeeController::class, 'update']);
        Route::delete('/{employee}',       [EmployeeController::class, 'destroy']);
        Route::patch('/{employee}/status', [EmployeeController::class, 'updateStatus']);
        Route::patch('/{employee}/role',   [EmployeeController::class, 'updateRole']);
        Route::post('/{id}/restore',       [EmployeeController::class, 'restore']);
        Route::delete('/{id}/purge',       [EmployeeController::class, 'purge']);
    });

    // ── Attendance — Admin + HR ──────────────────────────────────────
    Route::middleware('role:Admin,HR')->prefix('attendance')->group(function () {
        Route::get('/',              [AttendanceController::class, 'index']);
        Route::get('/live-status',   [AttendanceController::class, 'liveStatus']);
        Route::get('/monthly-stats', [AttendanceController::class, 'monthlyStats']);
        Route::get('/export',        [AttendanceController::class, 'export']);
        Route::post('/import',       [AttendanceController::class, 'import']);
        Route::post('/manual',       [AttendanceController::class, 'manual']);
    });

    // ── Leave — Admin + HR ───────────────────────────────────────────
    Route::middleware('role:Admin,HR')->prefix('leave-requests')->group(function () {
        Route::get('/',              [LeaveController::class, 'index']);
        Route::post('/',             [LeaveController::class, 'store']);
        Route::get('/pending',       [LeaveController::class, 'pending']);
        Route::post('/{id}/approve', [LeaveController::class, 'approve']);
        Route::post('/{id}/reject',  [LeaveController::class, 'reject']);
        Route::post('/{id}/cancel',  [LeaveController::class, 'cancel']);
    });
    Route::middleware('role:Admin,HR')->prefix('leave-balances')->group(function () {
        Route::get('/',        [LeaveController::class, 'balances']);
        Route::post('/seed',   [LeaveController::class, 'seedBalances']);
        Route::post('/accrue', [LeaveController::class, 'accrue']);
    });

    // ── Payroll — Accountant only ────────────────────────────────────
    Route::middleware('role:Accountant')->group(function () {
        Route::prefix('payroll-periods')->group(function () {
            Route::get('/',                       [PayslipController::class, 'listPeriods']);
            Route::post('/',                      [PayslipController::class, 'createPeriod']);
            Route::post('/generate-next',         [PayslipController::class, 'generateNextPeriod']);
            Route::get('/{periodId}/summary-pdf', [PayslipController::class, 'summaryPdf']);
        });
        Route::prefix('payslips')->group(function () {
            Route::get('/',                      [PayslipController::class, 'index']);
            Route::get('/summary',               [PayslipController::class, 'summary']);
            Route::get('/audit-trail',           [PayslipController::class, 'auditTrail']);
            Route::post('/compute',              [PayslipController::class, 'computeSingle']);
            Route::post('/compute-all',          [PayslipController::class, 'computeAll']);
            Route::post('/bulk-send-email',      [PayslipController::class, 'bulkSendEmail']);
            Route::post('/approve-all/{period}', [PayslipController::class, 'approveAll']);
            Route::get('/{payslip}',             [PayslipController::class, 'show']);
            Route::post('/{payslip}/adjust',     [PayslipController::class, 'adjust']);
            Route::post('/{payslip}/approve',    [PayslipController::class, 'approve']);
            Route::post('/{payslip}/pay',        [PayslipController::class, 'markPaid']);
            Route::post('/{payslip}/send-email', [PayslipController::class, 'sendEmail']);
            Route::get('/{payslip}/pdf',         [PayslipController::class, 'downloadPdf']);
        });
    });

    // ── Performance — Admin + HR ─────────────────────────────────────
    Route::middleware('role:Admin,HR')->prefix('evaluations')->group(function () {
        Route::get('/my-assignments',                   [EvaluationFormController::class, 'myAssignments']);
        Route::get('/assignments/{assignment}',         [EvaluationFormController::class, 'getAssignment']);
        Route::post('/assignments/{assignment}/submit', [EvaluationFormController::class, 'submitAssignment']);
        Route::get('/',                                 [EvaluationFormController::class, 'index']);
        Route::post('/',                                [EvaluationFormController::class, 'store']);
        Route::get('/{form}/analytics',                 [EvaluationFormController::class, 'analytics']);
        Route::get('/{form}',                           [EvaluationFormController::class, 'show']);
        Route::put('/{form}',                           [EvaluationFormController::class, 'update']);
        Route::delete('/{form}',                        [EvaluationFormController::class, 'destroy']);
        Route::post('/{form}/close',                    [EvaluationFormController::class, 'close']);
        Route::post('/{form}/send',                     [EvaluationFormController::class, 'send']);
    });
    Route::middleware('role:Admin,HR')->prefix('performance')->group(function () {
        Route::get('/kpis',            [KpiController::class, 'index']);
        Route::post('/kpis',           [KpiController::class, 'store']);
        Route::get('/kpis/{kpi}',      [KpiController::class, 'show']);
        Route::put('/kpis/{kpi}',      [KpiController::class, 'update']);
        Route::delete('/kpis/{kpi}',   [KpiController::class, 'destroy']);
        Route::get('/goals',           [GoalController::class, 'index']);
        Route::post('/goals',          [GoalController::class, 'store']);
        Route::get('/goals/{goal}',    [GoalController::class, 'show']);
        Route::put('/goals/{goal}',    [GoalController::class, 'update']);
        Route::delete('/goals/{goal}', [GoalController::class, 'destroy']);
    });

    // ── Recruitment ───────────────────────────────────────────────────────────
    Route::prefix('recruitment')->group(function () {
        // Job Postings
        Route::get('/job-postings',            [RecruitmentController::class, 'getJobPostings']);
        Route::post('/job-postings',           [RecruitmentController::class, 'createJobPosting']);
        Route::put('/job-postings/{id}',       [RecruitmentController::class, 'updateJobPosting']);
        Route::delete('/job-postings/{id}',    [RecruitmentController::class, 'deleteJobPosting']);

        // Applicants
        Route::get('/applicants',              [RecruitmentController::class, 'getApplicants']);
        Route::post('/applicants',             [RecruitmentController::class, 'createApplicant']);
        Route::patch('/applicants/{id}/stage', [RecruitmentController::class, 'updateApplicantStage']);
        Route::post('/applicants/{id}/hire',   [RecruitmentController::class, 'hireApplicant']);
        Route::post('/applicants/{id}/reject', [RecruitmentController::class, 'rejectApplicant']);

        // Interviews
        Route::get('/interviews',                    [RecruitmentController::class, 'getInterviews']);
        Route::post('/interviews',                   [RecruitmentController::class, 'scheduleInterview']);
        Route::get('/interviewers',                  [RecruitmentController::class, 'getInterviewers']);
        Route::post('/interviews/{id}/complete',     [RecruitmentController::class, 'completeInterview']);

        // Training
        Route::get('/training-assignments',                          [RecruitmentController::class, 'getTrainingAssignments']);
        Route::post('/training-assignments/{id}/assign-trainer',     [RecruitmentController::class, 'assignTrainer']);
        Route::post('/training-assignments/{id}/complete',           [RecruitmentController::class, 'completeTraining']);

        // New Hires (recruitment side — kept for backward compat)
        Route::get('/new-hires',                                     [RecruitmentController::class, 'getNewHires']);
        Route::post('/new-hires/{id}/complete-details',              [RecruitmentController::class, 'completeNewHireDetails']);
        Route::post('/new-hires/{id}/transfer',                      [RecruitmentController::class, 'transferToEmployee']);
    });

});