<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Leave;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // ── Attendance Records (read-only) ──────────────────────
    public function index(Request $request)
    {
        $query = Attendance::with('employee.department')
            ->orderByDesc('date')
            ->orderBy('employee_id');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $records     = $query->paginate(20)->withQueryString();
        $employees   = Employee::active()->orderBy('first_name')->get();
        $departments = Department::orderBy('name')->get();

        // Summary counts for today
        $today        = now()->toDateString();
        $totalActive  = Employee::active()->count();
        $presentToday = Attendance::whereDate('date', $today)->where('status', 'present')->count();
        $absentToday  = Attendance::whereDate('date', $today)->where('status', 'absent')->count();
        $lateToday    = Attendance::whereDate('date', $today)->where('status', 'late')->count();

        return view('attendance.index', compact(
            'records', 'employees', 'departments',
            'totalActive', 'presentToday', 'absentToday', 'lateToday'
        ));
    }

    // ── Fetch / Simulate Import from Biometric System ───────
    public function fetch(Request $request)
    {
        // In production: replace this with a real API call to your
        // biometric device or HR system.
        // For now, this simulates importing today's attendance for all active employees.

        $today     = now()->toDateString();
        $employees = Employee::active()->get();
        $imported  = 0;

        foreach ($employees as $employee) {
            // Skip if already fetched today
            $exists = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->exists();

            if ($exists) continue;

            // Simulate random attendance data
            $statuses  = ['present', 'present', 'present', 'present', 'late', 'absent'];
            $status    = $statuses[array_rand($statuses)];
            $timeIn    = null;
            $timeOut   = null;
            $hours     = 0;

            if ($status !== 'absent') {
                $inHour   = $status === 'late' ? rand(9, 10) : 8;
                $inMin    = $status === 'late' ? rand(5, 59) : rand(0, 30);
                $outHour  = 17;
                $outMin   = rand(0, 30);
                $timeIn   = sprintf('%02d:%02d:00', $inHour, $inMin);
                $timeOut  = sprintf('%02d:%02d:00', $outHour, $outMin);
                $hours    = round(($outHour + $outMin / 60) - ($inHour + $inMin / 60), 2);
            }

            Attendance::create([
                'employee_id'  => $employee->id,
                'date'         => $today,
                'time_in'      => $timeIn,
                'time_out'     => $timeOut,
                'hours_worked' => $hours,
                'status'       => $status,
            ]);

            $imported++;
        }

        $message = $imported > 0
            ? "Successfully fetched attendance for {$imported} employee(s) for today."
            : "Attendance for today has already been fetched. No new records imported.";

        return back()->with('success', $message);
    }

    // ── Schedules ────────────────────────────────────────────
    public function schedules(Request $request)
    {
        $query = Schedule::with('employee.department')
            ->where('status', 'active');

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $schedules   = $query->paginate(20)->withQueryString();
        $employees   = Employee::active()->orderBy('first_name')->get();
        $departments = Department::orderBy('name')->get();

        return view('attendance.schedules', compact('schedules', 'employees', 'departments'));
    }

    // ── Store Schedule ────────────────────────────────────────
    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'shift_start'    => 'required|date_format:H:i',
            'shift_end'      => 'required|date_format:H:i',
            'days'           => 'required|array|min:1',
            'effective_date' => 'required|date',
        ]);

        // Deactivate previous schedule for same employee
        Schedule::where('employee_id', $validated['employee_id'])
            ->where('status', 'active')
            ->update(['status' => 'inactive']);

        Schedule::create([
            'employee_id'    => $validated['employee_id'],
            'shift_start'    => $validated['shift_start'],
            'shift_end'      => $validated['shift_end'],
            'days'           => implode(',', $validated['days']),
            'effective_date' => $validated['effective_date'],
            'status'         => 'active',
        ]);

        return back()->with('success', 'Schedule assigned successfully.');
    }

    // ── Update Schedule ───────────────────────────────────────
    public function updateSchedule(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'shift_start'    => 'required|date_format:H:i',
            'shift_end'      => 'required|date_format:H:i',
            'days'           => 'required|array|min:1',
            'effective_date' => 'required|date',
        ]);

        $schedule->update([
            'shift_start'    => $validated['shift_start'],
            'shift_end'      => $validated['shift_end'],
            'days'           => implode(',', $validated['days']),
            'effective_date' => $validated['effective_date'],
        ]);

        return back()->with('success', 'Schedule updated successfully.');
    }

    // ── Leave Requests ────────────────────────────────────────
    public function leaves(Request $request)
    {
        $query = Leave::with('employee.department', 'approvedBy')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $leaves      = $query->paginate(15)->withQueryString();
        $employees   = Employee::active()->orderBy('first_name')->get();
        $departments = Department::orderBy('name')->get();

        $pendingCount  = Leave::where('status', 'pending')->count();
        $approvedCount = Leave::where('status', 'approved')->count();
        $rejectedCount = Leave::where('status', 'rejected')->count();

        return view('attendance.leaves', compact(
            'leaves', 'employees', 'departments',
            'pendingCount', 'approvedCount', 'rejectedCount'
        ));
    }

    // ── Store Leave Request ───────────────────────────────────
    public function storeLeave(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|string|max:50',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'nullable|string|max:500',
        ]);

        Leave::create($validated + ['status' => 'pending']);

        return back()->with('success', 'Leave request submitted successfully.');
    }

    // ── Approve Leave ─────────────────────────────────────────
    public function approveLeave(Leave $leave)
    {
        $leave->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', "{$leave->employee->full_name}'s leave request approved.");
    }

    // ── Reject Leave ──────────────────────────────────────────
    public function rejectLeave(Leave $leave)
    {
        $leave->update([
            'status'      => 'rejected',
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', "{$leave->employee->full_name}'s leave request rejected.");
    }
}