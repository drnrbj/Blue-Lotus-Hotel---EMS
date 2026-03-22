@extends('layouts.app')

@section('title', 'Attendance')
@section('subtitle', 'Manage employee leave requests')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')

@if(session('success'))
    <div class="mb-5 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
        <svg width="16" height="16" fill="none" viewBox="0 0 16 16"><path d="M3 8l3 3 7-7" stroke="#16a34a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        {{ session('success') }}
    </div>
@endif

@include('attendance._tabs', ['active' => 'leaves'])

{{-- Summary Counts --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Pending</p>
        <p class="text-[26px] font-bold text-amber-500">{{ $pendingCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Awaiting approval</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Approved</p>
        <p class="text-[26px] font-bold text-green-600">{{ $approvedCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">This period</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Rejected</p>
        <p class="text-[26px] font-bold text-red-500">{{ $rejectedCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">This period</p>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-200" x-data="{ showFile: false }">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">

            <div class="flex-shrink-0">
                <h2 class="text-[15px] font-bold text-gray-900">Leave Requests</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">Review and action employee leave applications</p>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('attendance.leaves') }}"
                  class="flex flex-wrap items-center gap-2 lg:ml-auto">

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
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                <button type="submit"
                        class="h-9 px-4 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90 transition-colors">
                    Filter
                </button>

                @if(request()->hasAny(['status','department_id','employee_id']))
                    <a href="{{ route('attendance.leaves') }}"
                       class="h-9 px-3 flex items-center gap-1.5 border border-gray-300 rounded-lg text-[13px] text-gray-500 hover:bg-gray-50">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        Clear
                    </a>
                @endif

            </form>

            {{-- File Leave Button --}}
            <button type="button" @click="showFile = true"
                    class="flex-shrink-0 flex items-center gap-2 h-9 px-4 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500 transition-colors">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M7 1v12M1 7h12" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                File Leave Request
            </button>

        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">#</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Employee</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Leave Type</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Duration</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Days</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Reason</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($leaves as $leave)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-[13px] text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-sky text-[11px] font-bold flex-shrink-0">
                                    {{ $leave->employee->initials ?? '??' }}
                                </div>
                                <div>
                                    <p class="text-[13px] font-medium text-gray-900">{{ $leave->employee->full_name ?? '—' }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $leave->employee->department->name ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $typeColors = [
                                    'Sick Leave'      => 'bg-blue-100 text-blue-700',
                                    'Vacation Leave'  => 'bg-teal-100 text-teal-700',
                                    'Emergency Leave' => 'bg-red-100 text-red-600',
                                    'Maternity Leave' => 'bg-pink-100 text-pink-700',
                                    'Paternity Leave' => 'bg-indigo-100 text-indigo-700',
                                ];
                                $tc = $typeColors[$leave->leave_type] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $tc }}">
                                {{ $leave->leave_type }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-700">
                            {{ $leave->start_date->format('d/m/Y') }}
                            @if(!$leave->start_date->eq($leave->end_date))
                                – {{ $leave->end_date->format('d/m/Y') }}
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-[13px] font-medium text-gray-800">
                            {{ $leave->duration }}d
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-500 max-w-[180px]">
                            <p class="truncate">{{ $leave->reason ?? '—' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($leave->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                </span>
                            @elseif($leave->status === 'approved')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Approved
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($leave->status === 'pending')
                                <div class="flex items-center gap-2">
                                    {{-- Approve --}}
                                    <form method="POST" action="{{ route('attendance.leaves.approve', $leave) }}">
                                        @csrf
                                        <button type="submit"
                                                class="flex items-center gap-1 h-7 px-2.5 bg-green-100 hover:bg-green-200 text-green-700 text-[11px] font-semibold rounded-md transition-colors">
                                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            Approve
                                        </button>
                                    </form>
                                    {{-- Reject --}}
                                    <form method="POST" action="{{ route('attendance.leaves.reject', $leave) }}">
                                        @csrf
                                        <button type="submit"
                                                class="flex items-center gap-1 h-7 px-2.5 bg-red-100 hover:bg-red-200 text-red-600 text-[11px] font-semibold rounded-md transition-colors">
                                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 2l8 8M10 2L2 10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-[12px] text-gray-400">
                                    by {{ $leave->approvedBy->name ?? '—' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-14 text-center text-[13px] text-gray-400">
                            No leave requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($leaves->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $leaves->links() }}</div>
    @endif


    {{-- ── MODAL: File Leave Request ── --}}
    <div x-show="showFile"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showFile = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-[16px] font-bold text-gray-900">File Leave Request</h3>
                    <p class="text-[12px] text-gray-400 mt-0.5">Submit a new leave application</p>
                </div>
                <button @click="showFile = false" class="text-gray-400 hover:text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('attendance.leaves.store') }}" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id"
                            class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                        <option value="">Select employee...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} — {{ $emp->position }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Leave Type <span class="text-red-500">*</span></label>
                    <select name="leave_type"
                            class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                        <option value="">Select type...</option>
                        <option value="Sick Leave">Sick Leave</option>
                        <option value="Vacation Leave">Vacation Leave</option>
                        <option value="Emergency Leave">Emergency Leave</option>
                        <option value="Maternity Leave">Maternity Leave</option>
                        <option value="Paternity Leave">Paternity Leave</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">End Date <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Reason</label>
                    <textarea name="reason" rows="3" placeholder="Optional reason..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showFile = false"
                            class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit"
                            class="h-9 px-5 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection