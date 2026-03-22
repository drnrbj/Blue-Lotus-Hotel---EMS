<?php

namespace App\Http\Controllers;

use App\Models\TrainingProgram;
use App\Models\TrainingParticipant;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    // ── Training Programs ───────────────────────────────────
    public function index(Request $request)
    {
        $query = TrainingProgram::with('department')
            ->withCount('participants')
            ->orderByDesc('start_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $programs    = $query->paginate(12)->withQueryString();
        $departments = Department::orderBy('name')->get();

        $upcomingCount  = TrainingProgram::where('status', 'upcoming')->count();
        $ongoingCount   = TrainingProgram::where('status', 'ongoing')->count();
        $completedCount = TrainingProgram::where('status', 'completed')->count();

        return view('training.index', compact(
            'programs', 'departments',
            'upcomingCount', 'ongoingCount', 'completedCount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_name'  => 'required|string|max:150',
            'department_id' => 'nullable|exists:departments,id',
            'description'   => 'nullable|string|max:1000',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);

        TrainingProgram::create($validated + ['status' => 'upcoming']);
        return back()->with('success', "Training program \"{$validated['program_name']}\" created.");
    }

    public function update(Request $request, TrainingProgram $program)
    {
        $validated = $request->validate([
            'program_name'  => 'required|string|max:150',
            'department_id' => 'nullable|exists:departments,id',
            'description'   => 'nullable|string|max:1000',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'status'        => 'required|in:upcoming,ongoing,completed',
        ]);

        $program->update($validated);
        return back()->with('success', "Training program updated.");
    }

    public function destroy(TrainingProgram $program)
    {
        $program->delete();
        return back()->with('success', "Training program deleted.");
    }

    // ── Participants ────────────────────────────────────────
    public function participants(Request $request)
    {
        $query = TrainingParticipant::with('training.department', 'employee.department')
            ->orderByDesc('training_id');

        if ($request->filled('training_id')) {
            $query->where('training_id', $request->training_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department_id')) {
            $query->whereHas('employee', fn($q) =>
                $q->where('department_id', $request->department_id)
            );
        }

        $participants = $query->paginate(15)->withQueryString();
        $programs     = TrainingProgram::whereIn('status', ['upcoming','ongoing'])->orderBy('program_name')->get();
        $employees    = Employee::active()->orderBy('first_name')->get();
        $departments  = Department::orderBy('name')->get();
        $allPrograms  = TrainingProgram::orderBy('program_name')->get();

        $ongoingCount   = TrainingParticipant::where('status', 'ongoing')->count();
        $completedCount = TrainingParticipant::where('status', 'completed')->count();

        return view('training.participants', compact(
            'participants', 'programs', 'employees', 'departments', 'allPrograms',
            'ongoingCount', 'completedCount'
        ));
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'training_id'  => 'required|exists:training_programs,id',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $enrolled = 0;
        $skipped  = 0;

        foreach ($request->employee_ids as $empId) {
            $exists = TrainingParticipant::where('training_id', $request->training_id)
                ->where('employee_id', $empId)->exists();

            if ($exists) { $skipped++; continue; }

            TrainingParticipant::create([
                'training_id' => $request->training_id,
                'employee_id' => $empId,
                'status'      => 'ongoing',
            ]);
            $enrolled++;
        }

        $msg = "{$enrolled} employee(s) enrolled.";
        if ($skipped > 0) $msg .= " {$skipped} skipped (already enrolled).";

        return back()->with('success', $msg);
    }

    public function markComplete(TrainingParticipant $participant)
    {
        $participant->update(['status' => 'completed']);
        return back()->with('success', "{$participant->employee->full_name} marked as completed.");
    }

    public function removeParticipant(TrainingParticipant $participant)
    {
        $name = $participant->employee->full_name;
        $participant->delete();
        return back()->with('success', "{$name} removed from training.");
    }
}