@extends('layouts.app')

@section('title', 'Attendance')
@section('subtitle', 'Daily attendance records from the biometric system')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')

{{-- Flash --}}
@if(session('success'))
    <div class="mb-5 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
        <svg width="16" height="16" fill="none" viewBox="0 0 16 16"><path d="M3 8l3 3 7-7" stroke="#16a34a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Sub-nav tabs --}}
@include('attendance._tabs', ['active' => 'attendance'])

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Total Active</p>
        <p class="text-[26px] font-bold text-gray-900">{{ $totalActive }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Employees</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Present Today</p>
        <p class="text-[26px] font-bold text-green-600">{{ $presentToday }}</p>
        <p class="text-[12px] text-gray-400 mt-1">On time</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Late Today</p>
        <p class="text-[26px] font-bold text-amber-500">{{ $lateToday }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Past shift start</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Absent Today</p>
        <p class="text-[26px] font-bold text-red-500">{{ $absentToday }}</p>
        <p class="text-[12px] text-gray-400 mt-1">No record</p>
    </div>
</div>

{{-- Main Table Card --}}
<div class="bg-white rounded-lg border border-gray-200">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">

            <div class="flex-shrink-0">
                <h2 class="text-[15px] font-bold text-gray-900">Attendance Records</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">Read-only · Imported from biometric system</p>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('attendance.index') }}"
                  class="flex flex-wrap items-center gap-2 lg:ml-auto">

                <input type="date" name="date" value="{{ request('date') }}"
                       class="h-9 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">

                <select name="department_id"
                    class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none"
                    style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status"
                    class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none"
                    style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Status</option>
                    <option value="present"  {{ request('status') === 'present'  ? 'selected' : '' }}>Present</option>
                    <option value="late"     {{ request('status') === 'late'     ? 'selected' : '' }}>Late</option>
                    <option value="absent"   {{ request('status') === 'absent'   ? 'selected' : '' }}>Absent</option>
                </select>

                <button type="submit"
                        class="h-9 px-4 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90 transition-colors">
                    Filter
                </button>

                @if(request()->hasAny(['date','department_id','status','employee_id']))
                    <a href="{{ route('attendance.index') }}"
                       class="h-9 px-3 flex items-center gap-1.5 border border-gray-300 rounded-lg text-[13px] text-gray-500 hover:bg-gray-50">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        Clear
                    </a>
                @endif

            </form>

            {{-- Fetch Button --}}
            <form method="POST" action="{{ route('attendance.fetch') }}" class="flex-shrink-0">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 h-9 px-4 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500 transition-colors"
                        onclick="return confirm('Fetch today\'s attendance from the biometric system?')">
                    <svg width="15" height="15" viewBox="0 0 17 17" fill="none">
                        <path d="M14 8.5A5.5 5.5 0 118.5 3" stroke="white" stroke-width="1.6" stroke-linecap="round"/>
                        <path d="M14 3v5h-5" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Fetch Attendance
                </button>
            </form>

        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">#</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Employee</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Department</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Date</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Time In</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Time Out</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Hours</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($records as $record)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-[13px] text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-sky text-[11px] font-bold flex-shrink-0">
                                    {{ $record->employee->initials ?? '??' }}
                                </div>
                                <div>
                                    <p class="text-[13px] font-medium text-gray-900">{{ $record->employee->full_name ?? '—' }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $record->employee->employee_code ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-500">{{ $record->employee->department->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-700">{{ $record->date->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-700">
                            {{ $record->time_in ? \Carbon\Carbon::parse($record->time_in)->format('h:i A') : '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-700">
                            {{ $record->time_out ? \Carbon\Carbon::parse($record->time_out)->format('h:i A') : '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-700">
                            {{ $record->hours_worked ? number_format($record->hours_worked, 1) . 'h' : '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($record->status === 'present')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Present
                                </span>
                            @elseif($record->status === 'late')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Late
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Absent
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" class="text-gray-300">
                                    <rect x="6" y="8" width="28" height="26" rx="3" stroke="currentColor" stroke-width="2"/>
                                    <path d="M13 4v8M27 4v8M6 18h28" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M13 27l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p class="text-[13px] text-gray-400">No attendance records found.</p>
                                <form method="POST" action="{{ route('attendance.fetch') }}">
                                    @csrf
                                    <button type="submit" class="text-[13px] text-sky font-semibold hover:underline">
                                        Fetch today's attendance →
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($records->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $records->links() }}</div>
    @endif

</div>

@endsection