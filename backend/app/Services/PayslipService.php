<?php
// backend/app/Services/PayslipService.php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\PayrollAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PayslipService
{
    const WORKING_DAYS_PER_MONTH = 26;

    /**
     * Compute payslip for a single employee
     */

    // app/Services/PayslipService.php - Updated compute() method

    public function compute(Employee $employee, PayrollPeriod $period, int $computedBy): Payslip
    {
        Log::info("Computing payslip for: {$employee->first_name} {$employee->last_name}");

        return DB::transaction(function () use ($employee, $period, $computedBy) {
            $workingDays = $this->getWorkingDaysInPeriod($period);
            $monthlySalary = (float) $employee->basic_salary;
            $dailyRate = $monthlySalary / self::WORKING_DAYS_PER_MONTH;
            $basicPay = $dailyRate * $workingDays;

            // ─────────────────────────────────────────────────────────
            // SSS Contribution (MSC-based as of 2024)
            // Employee: 4.5% of MSC, Employer: 9.5% of MSC
            // MSC range: ₱4,000 – ₱30,000
            // ─────────────────────────────────────────────────────────
            $msc = min(30000, max(4000, $monthlySalary));
            $sssEmployee = round($msc * 0.045);
            $sssEmployer = round($msc * 0.095);

            // ─────────────────────────────────────────────────────────
            // PhilHealth (5% of basic salary, split 50/50)
            // Min premium base: ₱10,000, Max: ₱100,000
            // ─────────────────────────────────────────────────────────
            $philBase = min(100000, max(10000, $monthlySalary));
            $philTotal = $philBase * 0.05;
            $philhealthEmployee = round($philTotal / 2);
            $philhealthEmployer = round($philTotal / 2);

            // ─────────────────────────────────────────────────────────
            // Pag-IBIG (2% employee, 2% employer)
            // Max contribution base: ₱5,000
            // Employee max: ₱100, Employer max: ₱100
            // ─────────────────────────────────────────────────────────
            $pagibigBase = min(5000, $monthlySalary);
            $pagibigEmployee = round($pagibigBase * 0.02); // Max ₱100
            $pagibigEmployer = round($pagibigBase * 0.02); // Max ₱100

            // For semi-monthly, halve the amounts BEFORE tax calculation
            if ($period->type === 'semi_monthly') {
                $basicPay = $basicPay / 2;
                $sssEmployee = $sssEmployee / 2;
                $sssEmployer = $sssEmployer / 2;
                $philhealthEmployee = $philhealthEmployee / 2;
                $philhealthEmployer = $philhealthEmployer / 2;
                $pagibigEmployee = $pagibigEmployee / 2;
                $pagibigEmployer = $pagibigEmployer / 2;
            }

            $grossPay = $basicPay;

            // ─────────────────────────────────────────────────────────
            // BIR Withholding Tax — TRAIN Law (RA 10963)
            // Semi-monthly thresholds (₱10,416.67 = ₱125,000/yr = exempt)
            // No tax for most Davao hotel staff earning below ₱125k/year
            // ─────────────────────────────────────────────────────────
            $taxableIncome = $grossPay - $sssEmployee - $philhealthEmployee - $pagibigEmployee;
            $taxableIncome = max(0, $taxableIncome);

            // No withholding tax if gross pay (before deductions) is ₱10,000 or below
            // Also covers TRAIN Law: annual taxable ≤ ₱250,000 (≈ ₱20,833/mo) = zero tax
            if ($grossPay <= 10000 || $taxableIncome <= 0) {
                $bir = 0;
            } else {
                // Annualize for TRAIN Law bracket lookup
                $annualTaxable = $taxableIncome * ($period->type === 'semi_monthly' ? 24 : 12);

                if ($annualTaxable <= 250000) {
                    $annualTax = 0;
                } elseif ($annualTaxable <= 400000) {
                    $annualTax = ($annualTaxable - 250000) * 0.15;
                } elseif ($annualTaxable <= 800000) {
                    $annualTax = 22500 + ($annualTaxable - 400000) * 0.20;
                } elseif ($annualTaxable <= 2000000) {
                    $annualTax = 102500 + ($annualTaxable - 800000) * 0.25;
                } elseif ($annualTaxable <= 8000000) {
                    $annualTax = 402500 + ($annualTaxable - 2000000) * 0.30;
                } else {
                    $annualTax = 2202500 + ($annualTaxable - 8000000) * 0.35;
                }

                // Convert back to per-period amount
                $bir = round($annualTax / ($period->type === 'semi_monthly' ? 24 : 12));
            }

            $totalDeductions = $sssEmployee + $philhealthEmployee + $pagibigEmployee + $bir;
            $netPay = $grossPay - $totalDeductions;

            // Create or update payslip
            $payslip = Payslip::updateOrCreate(
                [
                    'payroll_period_id' => $period->id,
                    'employee_id' => $employee->id,
                ],
                [
                    'working_days_in_period' => $workingDays,
                    'days_worked' => $workingDays,
                    'days_absent' => 0,
                    'days_on_leave' => 0,
                    'days_unpaid_leave' => 0,
                    'minutes_late' => 0,
                    'overtime_hours' => 0,
                    'basic_pay' => round($basicPay, 2),
                    'overtime_pay' => 0,
                    'transport_allowance' => 0,
                    'meal_allowance' => 0,
                    'other_allowances' => 0,
                    'bonuses' => 0,
                    'thirteenth_month_pay' => 0,
                    'gross_pay' => round($grossPay, 2),
                    'late_deduction' => 0,
                    'absent_deduction' => 0,
                    'unpaid_leave_deduction' => 0,
                    'sss_employee' => round($sssEmployee, 2),
                    'philhealth_employee' => round($philhealthEmployee, 2),
                    'pagibig_employee' => round($pagibigEmployee, 2),
                    'bir_withholding_tax' => $bir,
                    'sss_employer' => round($sssEmployer, 2),
                    'philhealth_employer' => round($philhealthEmployer, 2),
                    'pagibig_employer' => round($pagibigEmployer, 2),
                    'sss_loan_deduction' => 0,
                    'pagibig_loan_deduction' => 0,
                    'company_loan_deduction' => 0,
                    'other_deductions' => 0,
                    'total_deductions' => round($totalDeductions, 2),
                    'net_pay' => round($netPay, 2),
                    'status' => 'computed',
                    'computed_by' => $computedBy,
                    'computed_at' => now(),
                ]
            );

            PayrollAuditLog::record(
                'payslip',
                $payslip->id,
                'computed',
                $computedBy,
                [],
                [
                    'gross_pay' => $grossPay,
                    'net_pay' => $netPay,
                    'sss' => $sssEmployee,
                    'philhealth' => $philhealthEmployee,
                    'pagibig' => $pagibigEmployee,
                    'tax' => $bir
                ],
                "Payslip computed for {$employee->full_name} - Net pay: ₱" . number_format($netPay, 2)
            );

            return $payslip;
        });
    }
    /**
     * Compute payslips for all active employees
     */
    public function computeAll(PayrollPeriod $period, int $computedBy): array
    {
        $employees = Employee::where('status', 'active')->get();
        $results = ['success' => [], 'failed' => []];

        Log::info("Starting bulk payroll computation for period: {$period->label}");

        foreach ($employees as $employee) {
            try {
                $payslip = $this->compute($employee, $period, $computedBy);
                $results['success'][] = [
                    'employee_id' => $employee->id,
                    'name' => $employee->full_name,
                    'net_pay' => $payslip->net_pay,
                ];
            } catch (\Throwable $e) {
                Log::error("Failed to compute for {$employee->first_name} {$employee->last_name}: " . $e->getMessage());
                $results['failed'][] = [
                    'employee_id' => $employee->id,
                    'name' => $employee->full_name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        Log::info("Bulk payroll completed: " . count($results['success']) . " success, " . count($results['failed']) . " failed");

        return $results;
    }

    /**
     * Add manual adjustment to payslip
     */
    public function addManualAdjustment(Payslip $payslip, string $category, string $label, float $amount, string $note, int $adjustedBy): Payslip
    {
        $beforeGross = $payslip->gross_pay;
        $beforeNet = $payslip->net_pay;

        if ($category === 'earning') {
            $payslip->increment('bonuses', $amount);
            $payslip->increment('gross_pay', $amount);
        } else {
            $payslip->increment('other_deductions', $amount);
            $payslip->increment('total_deductions', $amount);
        }

        $payslip->net_pay = $payslip->gross_pay - $payslip->total_deductions;
        $payslip->save();

        PayrollAuditLog::record(
            'payslip',
            $payslip->id,
            'adjusted',
            $adjustedBy,
            ['gross_pay' => $beforeGross, 'net_pay' => $beforeNet],
            ['gross_pay' => $payslip->gross_pay, 'net_pay' => $payslip->net_pay],
            $note
        );

        return $payslip->fresh();
    }

    /**
     * Get number of working days in a period
     */
    private function getWorkingDaysInPeriod(PayrollPeriod $period): int
    {
        $start = Carbon::parse($period->period_start);
        $end = Carbon::parse($period->period_end);
        $workingDays = 0;

        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }
}