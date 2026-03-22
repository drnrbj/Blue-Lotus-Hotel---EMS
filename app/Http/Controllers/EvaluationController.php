<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    // Evaluation criteria categories (fixed set used across all evaluations)
    public const CRITERIA = [
        'work_quality'      => 'Work Quality',
        'productivity'      => 'Productivity',
        'communication'     => 'Communication',
        'teamwork'          => 'Teamwork',
        'punctuality'       => 'Punctuality & Attendance',
        'initiative'        => 'Initiative',
    ];

    // ── Evaluation List ─────────────────────────────────────
    public function index(Request $request)
    {
        $query = Evaluation::with('employee.department', 'evaluator')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', fn($q) =>
                $q->where('department_id', $request->department_id)
            );
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $evaluations = $query->paginate(15)->withQueryString();
        $employees   = Employee::active()->orderBy('first_name')->get();
        $departments = Department::orderBy('name')->get();
        $periods     = Evaluation::distinct()->pluck('period')->sort()->values();

        $totalCount     = Evaluation::count();
        $pendingCount   = Evaluation::where('status', 'pending')->count();
        $completedCount = Evaluation::where('status', 'completed')->count();
        $avgScore       = Evaluation::where('status', 'completed')->avg('score');

        return view('performance.index', compact(
            'evaluations', 'employees', 'departments', 'periods',
            'totalCount', 'pendingCount', 'completedCount', 'avgScore'
        ));
    }

    // ── Create Evaluation ───────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'  => 'required|exists:employees,id',
            'period'       => 'required|string|max:50',
        ]);

        // Prevent duplicate evaluation for same employee + period
        $exists = Evaluation::where('employee_id', $validated['employee_id'])
            ->where('period', $validated['period'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => 'An evaluation for this employee and period already exists.']);
        }

        Evaluation::create([
            'employee_id'  => $validated['employee_id'],
            'evaluator_id' => Auth::id(),
            'period'       => $validated['period'],
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Evaluation created and assigned successfully.');
    }

    // ── Submit Score (complete evaluation) ──────────────────
    public function score(Request $request, Evaluation $evaluation)
    {
        $request->validate([
            'criteria'        => 'required|array',
            'criteria.*'      => 'required|integer|min:1|max:5',
            'remarks'         => 'nullable|string|max:1000',
        ]);

        // Each criterion is rated 1–5; final score = (avg / 5) * 100
        $criteriaScores = $request->criteria;
        $avg            = array_sum($criteriaScores) / count($criteriaScores);
        $finalScore     = round(($avg / 5) * 100, 2);

        $evaluation->update([
            'criteria' => $criteriaScores,
            'score'    => $finalScore,
            'remarks'  => $request->remarks,
            'status'   => 'completed',
        ]);

        return back()->with('success', "Evaluation for {$evaluation->employee->full_name} completed. Score: {$finalScore}%");
    }

    // ── Delete Evaluation ───────────────────────────────────
    public function destroy(Evaluation $evaluation)
    {
        $name = $evaluation->employee->full_name;
        $evaluation->delete();
        return back()->with('success', "Evaluation for {$name} deleted.");
    }

    // ── Analytics ───────────────────────────────────────────
    public function analytics(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        $periods     = Evaluation::distinct()->pluck('period')->sort()->values();

        // Average score per department
        $deptScores = Department::withCount('employees')
            ->get()
            ->map(function ($dept) {
                $avg = Evaluation::whereHas('employee', fn($q) => $q->where('department_id', $dept->id))
                    ->where('status', 'completed')
                    ->avg('score');
                return [
                    'name'  => $dept->name,
                    'avg'   => round($avg ?? 0, 1),
                    'count' => Evaluation::whereHas('employee', fn($q) => $q->where('department_id', $dept->id))
                                   ->where('status', 'completed')->count(),
                ];
            })
            ->filter(fn($d) => $d['count'] > 0)
            ->sortByDesc('avg')
            ->values();

        // Rating distribution
        $distribution = [
            'Outstanding'          => Evaluation::where('status','completed')->where('score','>=',90)->count(),
            'Exceeds Expectations' => Evaluation::where('status','completed')->whereBetween('score',[75,89.99])->count(),
            'Meets Expectations'   => Evaluation::where('status','completed')->whereBetween('score',[60,74.99])->count(),
            'Needs Improvement'    => Evaluation::where('status','completed')->whereBetween('score',[45,59.99])->count(),
            'Unsatisfactory'       => Evaluation::where('status','completed')->where('score','<',45)->count(),
        ];

        // Top performers
        $topPerformers = Evaluation::with('employee.department')
            ->where('status', 'completed')
            ->orderByDesc('score')
            ->limit(5)
            ->get();

        // Criteria averages (across all completed evaluations)
        $completedWithCriteria = Evaluation::where('status', 'completed')
            ->whereNotNull('criteria')
            ->get();

        $criteriaAverages = [];
        foreach (self::CRITERIA as $key => $label) {
            $vals = $completedWithCriteria
                ->filter(fn($e) => isset($e->criteria[$key]))
                ->map(fn($e) => $e->criteria[$key]);
            $criteriaAverages[$label] = $vals->isNotEmpty()
                ? round($vals->avg(), 2)
                : 0;
        }

        $overallAvg     = Evaluation::where('status', 'completed')->avg('score');
        $completedCount = Evaluation::where('status', 'completed')->count();
        $pendingCount   = Evaluation::where('status', 'pending')->count();

        return view('performance.analytics', compact(
            'deptScores', 'distribution', 'topPerformers',
            'criteriaAverages', 'overallAvg', 'completedCount',
            'pendingCount', 'periods', 'departments'
        ));
    }
}