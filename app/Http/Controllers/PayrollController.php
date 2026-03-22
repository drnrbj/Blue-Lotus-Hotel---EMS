<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\PayrollAdjustment;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    // Deduction rates (Philippines standard)
    const SSS_RATE        = 0.045;  // 4.5% employee share
    const PHILHEALTH_RATE = 0.025;  // 2.5% employee share
    const PAGIBIG_RATE    = 0.02;   // 2% employee share
    const TAX_RATE        = 0.05;   // simplified flat withholding tax

    // ── Payroll List ────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Payroll::with('employee.department', 'adjustments')
            ->orderByDesc('period_end')
            ->orderBy('employee_id');

        if ($request->filled('period')) {
            [$start, $end] = explode('|', $request->period);
            $query->where('period_start', $start)->where('period_end', $end);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', fn($q) =>
                $q->where('department_id', $request->department_id)
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls    = $query->paginate(20)->withQueryString();
        $departments = Department::orderBy('name')->get();

        // Available periods (distinct period_start/end pairs)
        $periods = Payroll::selectRaw('period_start, period_end')
            ->distinct()
            ->orderByDesc('period_end')
            ->get()
            ->map(fn($p) => [
                'key'   => $p->period_start . '|' . $p->period_end,
                'label' => Carbon::parse($p->period_start)->format('M d') . ' – ' . Carbon::parse($p->period_end)->format('M d, Y'),
            ]);

        $totalGross    = Payroll::sum('gross_pay');
        $totalNet      = Payroll::sum('net_pay');
        $totalDeduct   = Payroll::sum('deductions');
        $draftCount    = Payroll::where('status', 'draft')->count();
        $releasedCount = Payroll::where('status', 'released')->count();

        return view('payroll.index', compact(
            'payrolls', 'departments', 'periods',
            'totalGross', 'totalNet', 'totalDeduct',
            'draftCount', 'releasedCount'
        ));
    }

    // ── Run Payroll ─────────────────────────────────────────
    public function run(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        $employees   = Employee::with('department')->active()->orderBy('first_name')->get();
        return view('payroll.run', compact('departments', 'employees'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'period_start'   => 'required|date',
            'period_end'     => 'required|date|after_or_equal:period_start',
            'hourly_rate'    => 'required|numeric|min:0',
            'employee_ids'   => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $start      = $request->period_start;
        $end        = $request->period_end;
        $hourlyRate = (float) $request->hourly_rate;
        $processed  = 0;
        $skipped    = 0;

        DB::transaction(function () use ($request, $start, $end, $hourlyRate, &$processed, &$skipped) {
            foreach ($request->employee_ids as $empId) {

                // Skip if payroll already exists for this employee + period
                $exists = Payroll::where('employee_id', $empId)
                    ->where('period_start', $start)
                    ->where('period_end', $end)
                    ->exists();

                if ($exists) { $skipped++; continue; }

                // Pull total hours from attendance for the period
                $totalHours = Attendance::where('employee_id', $empId)
                    ->whereBetween('date', [$start, $end])
                    ->whereIn('status', ['present', 'late'])
                    ->sum('hours_worked');

                $totalHours = (float) $totalHours;

                // Calculate pay
                $basicPay = round($totalHours * $hourlyRate, 2);

                // Standard deductions
                $sss        = round($basicPay * self::SSS_RATE, 2);
                $philhealth = round($basicPay * self::PHILHEALTH_RATE, 2);
                $pagibig    = round($basicPay * self::PAGIBIG_RATE, 2);
                $tax        = round($basicPay * self::TAX_RATE, 2);
                $totalDeductions = $sss + $philhealth + $pagibig + $tax;

                $grossPay = $basicPay; // bonuses added later via adjustments
                $netPay   = round($grossPay - $totalDeductions, 2);

                $payroll = Payroll::create([
                    'employee_id'  => $empId,
                    'period_start' => $start,
                    'period_end'   => $end,
                    'total_hours'  => $totalHours,
                    'basic_pay'    => $basicPay,
                    'gross_pay'    => $grossPay,
                    'deductions'   => $totalDeductions,
                    'net_pay'      => $netPay,
                    'status'       => 'draft',
                ]);

                // Store deduction breakdown as adjustments
                foreach ([
                    ['type' => 'deduction', 'amount' => $sss,        'description' => 'SSS Contribution'],
                    ['type' => 'deduction', 'amount' => $philhealth,  'description' => 'PhilHealth'],
                    ['type' => 'deduction', 'amount' => $pagibig,     'description' => 'Pag-IBIG'],
                    ['type' => 'deduction', 'amount' => $tax,         'description' => 'Withholding Tax'],
                ] as $adj) {
                    if ($adj['amount'] > 0) {
                        PayrollAdjustment::create($adj + ['payroll_id' => $payroll->id]);
                    }
                }

                $processed++;
            }
        });

        $msg = "Payroll processed for {$processed} employee(s).";
        if ($skipped > 0) $msg .= " {$skipped} skipped (already processed).";

        return redirect()->route('payroll.index')->with('success', $msg);
    }

    // ── Add Adjustment ──────────────────────────────────────
    public function storeAdjustment(Request $request, Payroll $payroll)
    {
        $request->validate([
            'type'        => 'required|in:bonus,deduction',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'required|string|max:150',
        ]);

        PayrollAdjustment::create([
            'payroll_id'  => $payroll->id,
            'type'        => $request->type,
            'amount'      => $request->amount,
            'description' => $request->description,
        ]);

        // Recalculate gross and net
        $payroll->load('adjustments');
        $bonuses    = $payroll->adjustments->where('type', 'bonus')->sum('amount');
        $deductions = $payroll->adjustments->where('type', 'deduction')->sum('amount');
        $grossPay   = round($payroll->basic_pay + $bonuses, 2);
        $netPay     = round($grossPay - $deductions, 2);

        $payroll->update([
            'gross_pay'  => $grossPay,
            'deductions' => $deductions,
            'net_pay'    => $netPay,
        ]);

        return back()->with('success', "Adjustment added to {$payroll->employee->full_name}'s payroll.");
    }

    // ── Delete Adjustment ───────────────────────────────────
    public function destroyAdjustment(PayrollAdjustment $adjustment)
    {
        $payroll = $adjustment->payroll;
        $adjustment->delete();

        // Recalculate
        $payroll->load('adjustments');
        $bonuses    = $payroll->adjustments->where('type', 'bonus')->sum('amount');
        $deductions = $payroll->adjustments->where('type', 'deduction')->sum('amount');
        $grossPay   = round($payroll->basic_pay + $bonuses, 2);
        $netPay     = round($grossPay - $deductions, 2);

        $payroll->update([
            'gross_pay'  => $grossPay,
            'deductions' => $deductions,
            'net_pay'    => $netPay,
        ]);

        return back()->with('success', 'Adjustment removed.');
    }

    // ── Release Payroll ─────────────────────────────────────
    public function release(Payroll $payroll)
    {
        $payroll->update([
            'status'      => 'released',
            'released_at' => now(),
            'released_by' => Auth::id(),
        ]);

        return back()->with('success', "Payroll for {$payroll->employee->full_name} released.");
    }

    // ── Release All (batch) ─────────────────────────────────
    public function releaseAll(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end'   => 'required|date',
        ]);

        $count = Payroll::where('period_start', $request->period_start)
            ->where('period_end', $request->period_end)
            ->where('status', 'draft')
            ->update([
                'status'      => 'released',
                'released_at' => now(),
                'released_by' => Auth::id(),
            ]);

        return back()->with('success', "{$count} payroll(s) released.");
    }

    // ── Reports ─────────────────────────────────────────────
    public function reports(Request $request)
    {
        $departments = Department::orderBy('name')->get();

        $periods = Payroll::selectRaw('period_start, period_end')
            ->distinct()
            ->orderByDesc('period_end')
            ->get()
            ->map(fn($p) => [
                'key'   => $p->period_start . '|' . $p->period_end,
                'label' => Carbon::parse($p->period_start)->format('M d') . ' – ' . Carbon::parse($p->period_end)->format('M d, Y'),
            ]);

        // Department payroll totals
        $deptTotals = Department::with('employees')
            ->get()
            ->map(function ($dept) {
                $total = Payroll::whereHas('employee', fn($q) =>
                    $q->where('department_id', $dept->id)
                )->sum('net_pay');
                return ['name' => $dept->name, 'total' => (float) $total];
            })
            ->filter(fn($d) => $d['total'] > 0)
            ->sortByDesc('total')
            ->values();

        // Monthly payroll totals (last 6 periods)
        $monthlyTotals = Payroll::selectRaw('period_start, period_end, SUM(gross_pay) as total_gross, SUM(net_pay) as total_net, SUM(deductions) as total_deductions, COUNT(*) as employee_count')
            ->groupBy('period_start', 'period_end')
            ->orderByDesc('period_end')
            ->limit(6)
            ->get();

        $overallGross  = Payroll::sum('gross_pay');
        $overallNet    = Payroll::sum('net_pay');
        $overallDeduct = Payroll::sum('deductions');
        $totalEmployeesPaid = Payroll::distinct('employee_id')->count('employee_id');

        return view('payroll.reports', compact(
            'departments', 'periods', 'deptTotals', 'monthlyTotals',
            'overallGross', 'overallNet', 'overallDeduct', 'totalEmployeesPaid'
        ));
    }
}