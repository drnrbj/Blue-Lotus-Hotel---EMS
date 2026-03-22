@extends('layouts.app')
@section('title', 'Payroll')
@section('subtitle', 'Payroll summaries and financial reports')

@section('content')

@include('payroll._tabs', ['active' => 'reports'])

{{-- Top KPIs --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Total Gross Paid</p>
        <p class="text-[24px] font-bold text-gray-900">₱{{ number_format($overallGross, 2) }}</p>
        <p class="text-[12px] text-gray-400 mt-1">All time</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Total Net Released</p>
        <p class="text-[24px] font-bold text-green-600">₱{{ number_format($overallNet, 2) }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Employee take-home</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Total Deductions</p>
        <p class="text-[24px] font-bold text-red-500">₱{{ number_format($overallDeduct, 2) }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Gov't contributions + tax</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Employees Paid</p>
        <p class="text-[24px] font-bold text-navy">{{ number_format($totalEmployeesPaid) }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Unique employees</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Payroll by Period --}}
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-[15px] font-bold text-gray-900">Payroll by Period</h2>
            <p class="text-[12px] text-gray-400 mt-0.5">Last 6 payroll runs</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Period</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Employees</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Gross</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Net</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($monthlyTotals as $row)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-3.5 text-[13px] font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($row->period_start)->format('M d') }} – {{ \Carbon\Carbon::parse($row->period_end)->format('M d, Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-[13px] text-gray-600 text-right">{{ $row->employee_count }}</td>
                            <td class="px-5 py-3.5 text-[13px] text-gray-800 text-right">₱{{ number_format($row->total_gross, 2) }}</td>
                            <td class="px-5 py-3.5 text-[13px] font-semibold text-green-700 text-right">₱{{ number_format($row->total_net, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-[13px] text-gray-400">No payroll data yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Department Payroll Totals --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-[15px] font-bold text-gray-900 mb-1">Net Pay by Department</h2>
        <p class="text-[12px] text-gray-400 mb-5">Total net pay distributed per department</p>

        @php $maxDept = collect($deptTotals)->max('total') ?: 1; @endphp

        @forelse($deptTotals as $dept)
            <div class="mb-4">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[13px] font-medium text-gray-700">{{ $dept['name'] }}</span>
                    <span class="text-[13px] font-bold text-gray-800">₱{{ number_format($dept['total'], 2) }}</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-navy rounded-full"
                         style="width: {{ round(($dept['total'] / $maxDept) * 100) }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-[13px] text-gray-400 py-8 text-center">No payroll data yet.</p>
        @endforelse
    </div>

</div>

{{-- Deduction Breakdown Table --}}
<div class="bg-white rounded-lg border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-[15px] font-bold text-gray-900">Government Contribution Summary</h2>
        <p class="text-[12px] text-gray-400 mt-0.5">Total amounts remitted per contribution type</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Contribution Type</th>
                    <th class="text-right px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Rate</th>
                    <th class="text-right px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Total Deducted</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php
                    $contributions = [
                        ['name' => 'SSS Contribution',  'rate' => '4.5%',  'desc' => 'SSS Contribution'],
                        ['name' => 'PhilHealth',         'rate' => '2.5%',  'desc' => 'PhilHealth'],
                        ['name' => 'Pag-IBIG',           'rate' => '2.0%',  'desc' => 'Pag-IBIG'],
                        ['name' => 'Withholding Tax',    'rate' => '5.0%',  'desc' => 'Withholding Tax'],
                    ];
                @endphp
                @foreach($contributions as $contrib)
                    @php
                        $total = \App\Models\PayrollAdjustment::where('description', $contrib['desc'])
                                    ->where('type', 'deduction')
                                    ->sum('amount');
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></div>
                                <span class="text-[13px] font-medium text-gray-800">{{ $contrib['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-[13px] text-gray-500 text-right">{{ $contrib['rate'] }}</td>
                        <td class="px-6 py-3.5 text-[13px] font-semibold text-red-600 text-right">₱{{ number_format($total, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="bg-gray-50/50">
                    <td class="px-6 py-3.5 text-[13px] font-bold text-gray-900" colspan="2">Total</td>
                    <td class="px-6 py-3.5 text-[13px] font-bold text-red-700 text-right">₱{{ number_format($overallDeduct, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection