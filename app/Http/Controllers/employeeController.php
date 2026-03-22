<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    // ── Employee List ───────────────────────────────────────
    public function index(Request $request)
    {
        $query = Employee::with('department')
            ->active(); // only show active employees (not terminated)

        // Filter: department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter: job title / position
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Filter: employment type
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        $employees   = $query->orderBy('id')->paginate(15)->withQueryString();
        $departments = Department::orderBy('name')->get();
        $positions   = Employee::active()->distinct()->pluck('position')->sort()->values();

        return view('employees.index', compact('employees', 'departments', 'positions'));
    }

    // ── Update Employee ─────────────────────────────────────
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'department_id'   => 'required|exists:departments,id',
            'position'        => 'required|string|max:100',
            'employment_type' => 'required|in:full-time,part-time,contract',
            'date_started'    => 'required|date',
            'email'           => 'required|email|unique:employees,email,' . $employee->id,
            'phone_number'    => 'nullable|string|max:20',
            'address'         => 'nullable|string',
            'date_of_birth'   => 'nullable|date',
        ]);

        $employee->update($validated);

        return back()->with('success', "Employee {$employee->full_name} updated successfully.");
    }

    // ── Terminate Employee ──────────────────────────────────
    public function terminate(Request $request, Employee $employee)
    {
        $request->validate([
            'last_working_day'   => 'required|date',
            'termination_reason' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $employee) {
            $employee->update([
                'status'             => 'inactive',
                'last_working_day'   => $request->last_working_day,
                'termination_reason' => $request->termination_reason,
                'terminated_at'      => now(),
            ]);

            // Soft delete — employee disappears from active queries
            $employee->delete();
        });

        return back()->with('success', "{$employee->full_name} has been terminated.");
    }

    // ── New Hires (applicants pending profile creation) ─────
    public function newHires(Request $request)
    {
        // New hires are applicants with status = 'hired'
        // who do NOT yet have an employee record
        $query = \App\Models\Applicant::where('status', 'hired');

        if ($request->filled('department_id')) {
            // applicants don't have dept yet — filter is optional placeholder
        }

        if ($request->filled('from')) {
            $query->whereDate('applied_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('applied_date', '<=', $request->to);
        }

        $newHires    = $query->orderByDesc('applied_date')->paginate(15)->withQueryString();
        $departments = Department::orderBy('name')->get();

        return view('employees.new-hires', compact('newHires', 'departments'));
    }

    // ── Create Employee Profile from New Hire ───────────────
    public function createProfile(Request $request, \App\Models\Applicant $applicant)
    {
        $validated = $request->validate([
            'employee_code'   => 'required|string|unique:employees,employee_code',
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'department_id'   => 'required|exists:departments,id',
            'position'        => 'required|string|max:100',
            'employment_type' => 'required|in:full-time,part-time,contract',
            'date_started'    => 'required|date',
            'email'           => 'required|email|unique:employees,email',
            'phone_number'    => 'nullable|string|max:20',
            'address'         => 'nullable|string',
            'date_of_birth'   => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $applicant) {
            Employee::create($validated);
            // Mark applicant as fully onboarded (remove from new hires queue)
            $applicant->update(['status' => 'onboarded']);
        });

        return back()->with('success', "Employee profile created for {$applicant->name}.");
    }
}