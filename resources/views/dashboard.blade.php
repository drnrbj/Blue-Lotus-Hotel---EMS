@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Welcome back. Here\'s what\'s happening today.')

@section('content')

{{-- ── Stat Cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

    {{-- Total Employees --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide">Total Employees</p>
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 17 17" fill="none">
                    <circle cx="6" cy="5.5" r="2.5" stroke="#45AEE4" stroke-width="1.5"/>
                    <path d="M1 14c0-2.8 2.2-4 5-4s5 1.2 5 4" stroke="#45AEE4" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M11.5 7.5h4M13.5 5.5v4" stroke="#45AEE4" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <p class="text-[28px] font-bold text-gray-900">{{ $totalEmployees ?? 248 }}</p>
        <p class="text-[12px] text-green-500 mt-1 font-medium">↑ 4 hired this month</p>
    </div>

    {{-- Present Today --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide">Present Today</p>
            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 17 17" fill="none">
                    <rect x="2" y="3" width="13" height="11" rx="1.5" stroke="#10b981" stroke-width="1.5"/>
                    <path d="M5 1.5v3M12 1.5v3M2 7h13" stroke="#10b981" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M5 10.5l2 1.5 4-3.5" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <p class="text-[28px] font-bold text-gray-900">{{ $presentToday ?? 215 }}</p>
        <p class="text-[12px] text-green-500 mt-1 font-medium">↑ 86.7% attendance rate</p>
    </div>

    {{-- On Leave --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide">On Leave</p>
            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 17 17" fill="none">
                    <circle cx="8.5" cy="8.5" r="6" stroke="#f59e0b" stroke-width="1.5"/>
                    <path d="M8.5 5v4M8.5 11.5v.5" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <p class="text-[28px] font-bold text-gray-900">{{ $onLeave ?? 33 }}</p>
        <p class="text-[12px] text-amber-500 mt-1 font-medium">↑ 5 vs last week</p>
    </div>

    {{-- Monthly Payroll --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide">Monthly Payroll</p>
            <div class="w-9 h-9 rounded-lg bg-navy/5 flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 17 17" fill="none">
                    <rect x="2" y="4" width="13" height="10" rx="1.5" stroke="#0F2453" stroke-width="1.5"/>
                    <path d="M2 7.5h13" stroke="#0F2453" stroke-width="1.5"/>
                    <path d="M5.5 11h2M10.5 11h1" stroke="#0F2453" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <p class="text-[28px] font-bold text-gray-900">₱1.2M</p>
        <p class="text-[12px] text-red-400 mt-1 font-medium">↓ ₱42,000 vs last month</p>
    </div>

</div>

{{-- ── Row 2: Recent Employees + Pending Leave ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Recent Employees Table --}}
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-[15px] font-bold text-gray-900">Recent Employees</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">Latest additions to the team</p>
            </div>
            <a href="#" class="text-[12px] text-sky font-semibold hover:underline">View all</a>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Name</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Department</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php
                    $recent = $recentEmployees ?? [
                        ['name' => 'Juan Dela Cruz',   'dept' => 'Front Office',    'type' => 'Full-time'],
                        ['name' => 'Maria Corpuz',     'dept' => 'Housekeeping',    'type' => 'Part-time'],
                        ['name' => 'Ryan Lim',         'dept' => 'Finance',         'type' => 'Contractual'],
                        ['name' => 'Ana Santos',       'dept' => 'Human Resources', 'type' => 'Full-time'],
                        ['name' => 'Pedro Torres',     'dept' => 'Food & Beverages','type' => 'Full-time'],
                    ];
                @endphp
                @foreach($recent as $emp)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-[13px] font-medium text-gray-900">
                            {{ is_array($emp) ? $emp['name'] : $emp->name }}
                        </td>
                        <td class="px-6 py-3 text-[13px] text-gray-500">
                            {{ is_array($emp) ? $emp['dept'] : $emp->department }}
                        </td>
                        <td class="px-6 py-3">
                            @php $type = is_array($emp) ? $emp['type'] : $emp->employment_type; @endphp
                            @if($type === 'Full-time')
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">Full-time</span>
                            @elseif($type === 'Part-time')
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-600">Part-time</span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold border border-gray-300 text-gray-600">Contractual</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pending Leave Requests --}}
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-[15px] font-bold text-gray-900">Pending Leave Requests</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">Requires your approval</p>
            </div>
            <a href="#" class="text-[12px] text-sky font-semibold hover:underline">Manage</a>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Employee</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Type</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Duration</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 text-[13px] font-medium text-gray-900">Liza Reyes</td>
                    <td class="px-6 py-3"><span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-600">Sick Leave</span></td>
                    <td class="px-6 py-3 text-[12px] text-gray-400">Mar 24–25</td>
                    <td class="px-6 py-3 text-right"><a href="#" class="text-[12px] text-sky font-semibold hover:underline">Review</a></td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 text-[13px] font-medium text-gray-900">Carlo Mendez</td>
                    <td class="px-6 py-3"><span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-600">Vacation</span></td>
                    <td class="px-6 py-3 text-[12px] text-gray-400">Mar 28–Apr 1</td>
                    <td class="px-6 py-3 text-right"><a href="#" class="text-[12px] text-sky font-semibold hover:underline">Review</a></td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 text-[13px] font-medium text-gray-900">Grace Tan</td>
                    <td class="px-6 py-3"><span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-50 text-red-500">Emergency</span></td>
                    <td class="px-6 py-3 text-[12px] text-gray-400">Mar 22</td>
                    <td class="px-6 py-3 text-right"><a href="#" class="text-[12px] text-sky font-semibold hover:underline">Review</a></td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 text-[13px] font-medium text-gray-900">Ben Flores</td>
                    <td class="px-6 py-3"><span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-600">Sick Leave</span></td>
                    <td class="px-6 py-3 text-[12px] text-gray-400">Mar 23</td>
                    <td class="px-6 py-3 text-right"><a href="#" class="text-[12px] text-sky font-semibold hover:underline">Review</a></td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3 text-[13px] font-medium text-gray-900">Diana Cruz</td>
                    <td class="px-6 py-3"><span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-600">Vacation</span></td>
                    <td class="px-6 py-3 text-[12px] text-gray-400">Apr 2–4</td>
                    <td class="px-6 py-3 text-right"><a href="#" class="text-[12px] text-sky font-semibold hover:underline">Review</a></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

{{-- ── Row 3: Department Headcount + Quick Actions ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Department Headcount --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-[15px] font-bold text-gray-900">Department Headcount</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">Staff distribution by department</p>
            </div>
        </div>
        @php
            $departments = $deptData ?? [
                ['name' => 'Housekeeping',    'count' => 85, 'max' => 85],
                ['name' => 'Front Office',    'count' => 62, 'max' => 85],
                ['name' => 'Food & Beverages','count' => 54, 'max' => 85],
                ['name' => 'Human Resources', 'count' => 25, 'max' => 85],
                ['name' => 'Finance',         'count' => 22, 'max' => 85],
            ];
        @endphp
        <div class="space-y-4">
            @foreach($departments as $dept)
                @php
                    $count = is_array($dept) ? $dept['count'] : $dept->count;
                    $name  = is_array($dept) ? $dept['name']  : $dept->name;
                    $pct   = round(($count / 85) * 100);
                @endphp
                <div>
                    <div class="flex justify-between mb-1.5">
                        <span class="text-[13px] font-medium text-gray-700">{{ $name }}</span>
                        <span class="text-[12px] text-gray-400">{{ $count }} staff</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-sky rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-[15px] font-bold text-gray-900 mb-5">Quick Actions</h2>
        <div class="grid grid-cols-2 gap-3">

            <a href="#" class="group flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:border-sky hover:bg-blue-50/40 transition-all">
                <div class="w-8 h-8 rounded-lg bg-blue-50 group-hover:bg-sky/20 flex items-center justify-center flex-shrink-0 transition-colors">
                    <svg width="16" height="16" viewBox="0 0 17 17" fill="none"><circle cx="6" cy="5.5" r="2.5" stroke="#45AEE4" stroke-width="1.5"/><path d="M1 14c0-2.8 2.2-4 5-4s5 1.2 5 4" stroke="#45AEE4" stroke-width="1.5" stroke-linecap="round"/><path d="M11.5 7.5h4M13.5 5.5v4" stroke="#45AEE4" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-gray-800">Add Employee</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Onboard new staff</p>
                </div>
            </a>

            <a href="#" class="group flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:border-sky hover:bg-blue-50/40 transition-all">
                <div class="w-8 h-8 rounded-lg bg-green-50 group-hover:bg-green-100 flex items-center justify-center flex-shrink-0 transition-colors">
                    <svg width="16" height="16" viewBox="0 0 17 17" fill="none"><rect x="2" y="4" width="13" height="10" rx="1.5" stroke="#10b981" stroke-width="1.5"/><path d="M2 7.5h13" stroke="#10b981" stroke-width="1.5"/><path d="M5.5 11h2M10.5 11h1" stroke="#10b981" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-gray-800">Run Payroll</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Process this month</p>
                </div>
            </a>

            <a href="#" class="group flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:border-sky hover:bg-blue-50/40 transition-all">
                <div class="w-8 h-8 rounded-lg bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center flex-shrink-0 transition-colors">
                    <svg width="16" height="16" viewBox="0 0 17 17" fill="none"><rect x="2" y="2" width="13" height="13" rx="1.5" stroke="#f59e0b" stroke-width="1.5"/><path d="M5.5 5.5h6M5.5 8.5h6M5.5 11.5h4" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-gray-800">Post Job</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Create a listing</p>
                </div>
            </a>

            <a href="#" class="group flex items-start gap-3 p-4 border border-gray-200 rounded-lg hover:border-sky hover:bg-blue-50/40 transition-all">
                <div class="w-8 h-8 rounded-lg bg-navy/5 group-hover:bg-navy/10 flex items-center justify-center flex-shrink-0 transition-colors">
                    <svg width="16" height="16" viewBox="0 0 17 17" fill="none"><path d="M2 12l3.5-3.5 3 3L13 5.5l2 2" stroke="#0F2453" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-gray-800">New Evaluation</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Start a review cycle</p>
                </div>
            </a>

        </div>
    </div>

</div>

@endsection