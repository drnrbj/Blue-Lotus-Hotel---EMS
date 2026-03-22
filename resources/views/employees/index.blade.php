@extends('layouts.app')

@section('title', 'Employee Management')
@section('subtitle', 'Manage your employees and their information')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')

{{-- ── Flash Messages ── --}}
@if(session('success'))
    <div class="mb-5 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
        <svg width="16" height="16" fill="none" viewBox="0 0 16 16"><path d="M3 8l3 3 7-7" stroke="#16a34a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- ── Employee List Card ── --}}
<div class="bg-white rounded-lg border border-gray-200" x-data="employeeManager()">

    {{-- Header Row: Title + Filters + Button --}}
    <div class="px-6 py-5 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">

            {{-- Title --}}
            <div class="flex-shrink-0">
                <h2 class="text-[17px] font-bold text-gray-900">Employee List</h2>
                <p class="text-[13px] text-gray-400 mt-0.5">Showing all employees</p>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('employees.index') }}" class="flex flex-wrap items-center gap-2 lg:ml-auto">

                <select name="department_id"
                    class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] text-gray-700 bg-white focus:outline-none focus:border-sky appearance-none cursor-pointer"
                    style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>

                <select name="position"
                    class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] text-gray-700 bg-white focus:outline-none focus:border-sky appearance-none cursor-pointer"
                    style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Job Titles</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                    @endforeach
                </select>

                <select name="employment_type"
                    class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] text-gray-700 bg-white focus:outline-none focus:border-sky appearance-none cursor-pointer"
                    style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Employment Types</option>
                    <option value="full-time"  {{ request('employment_type') === 'full-time'  ? 'selected' : '' }}>Full-time</option>
                    <option value="part-time"  {{ request('employment_type') === 'part-time'  ? 'selected' : '' }}>Part-time</option>
                    <option value="contract"   {{ request('employment_type') === 'contract'   ? 'selected' : '' }}>Contractual</option>
                </select>

                @if(request()->hasAny(['department_id','position','employment_type']))
                    <a href="{{ route('employees.index') }}"
                       class="h-9 px-3 flex items-center gap-1.5 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50 transition-colors">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        Clear
                    </a>
                @else
                    <button type="button"
                        class="h-9 px-3 flex items-center gap-1.5 border border-gray-300 rounded-lg text-[13px] text-gray-400 cursor-default">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        Clear
                    </button>
                @endif

                <button type="submit" class="hidden"></button>

            </form>

            {{-- Add New Hire Button --}}
            <a href="{{ route('employees.new-hires') }}"
               class="flex-shrink-0 flex items-center gap-2 h-9 px-4 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500 transition-colors">
                <svg width="15" height="15" viewBox="0 0 17 17" fill="none">
                    <circle cx="6" cy="5.5" r="2.5" stroke="white" stroke-width="1.5"/>
                    <path d="M1 14c0-2.8 2.2-4 5-4s5 1.2 5 4" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M12 7v4M14 9h-4" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Add New Hire
            </a>

        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide w-12">#</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Name</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Department</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Job Title</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Start Date</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($employees as $emp)
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        <td class="px-6 py-3.5 text-[13px] text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-3.5 text-[13.5px] font-medium text-gray-900">{{ $emp->full_name }}</td>
                        <td class="px-6 py-3.5 text-[13px] text-gray-500">{{ $emp->department->name ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-[13px] text-gray-700">{{ $emp->position }}</td>
                        <td class="px-6 py-3.5 text-[13px] text-gray-600">{{ $emp->date_started->format('d/m/Y') }}</td>
                        <td class="px-6 py-3.5">
                            @if($emp->employment_type === 'full-time')
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">Full-time</span>
                            @elseif($emp->employment_type === 'part-time')
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-600">Part-time</span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold border border-gray-300 text-gray-600">Contractual</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">

                                {{-- View --}}
                                <button type="button" title="View"
                                    @click="openView({{ $emp->toJson() }})"
                                    class="text-sky hover:text-blue-700 transition-colors">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                        <path d="M1.5 9s3-6 7.5-6 7.5 6 7.5 6-3 6-7.5 6-7.5-6-7.5-6z" stroke="currentColor" stroke-width="1.5"/>
                                        <circle cx="9" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </button>

                                {{-- Edit --}}
                                <button type="button" title="Edit"
                                    @click="openEdit({{ $emp->toJson() }})"
                                    class="text-green-500 hover:text-green-700 transition-colors">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                                        <path d="M11.5 2.5l3 3L5 15H2v-3L11.5 2.5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                    </svg>
                                </button>

                                {{-- Terminate --}}
                                <button type="button" title="Terminate"
                                    @click="openTerminate({{ $emp->toJson() }})"
                                    class="text-red-500 hover:text-red-700 transition-colors">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                                        <polyline points="3,4 4,4 14,4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M6 4V3h5v1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M5 4l.8 9h5.4L12 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <line x1="7" y1="8" x2="7" y2="11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        <line x1="10" y1="8" x2="10" y2="11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </button>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-[13px] text-gray-400">
                            No employees found. Try adjusting your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($employees->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $employees->links() }}
        </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════
         MODAL: VIEW EMPLOYEE
    ═══════════════════════════════════════════════════════ --}}
    <div x-show="showView"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40" @click="showView = false"></div>

        {{-- Modal --}}
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-[16px] font-bold text-gray-900">Employee Profile</h3>
                <button @click="showView = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>

            <div class="p-6">
                {{-- Avatar + Name --}}
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-full bg-navy flex items-center justify-center text-sky font-bold text-lg flex-shrink-0"
                         x-text="selected ? (selected.first_name[0] + selected.last_name[0]).toUpperCase() : ''"></div>
                    <div>
                        <p class="text-[16px] font-bold text-gray-900" x-text="selected ? selected.first_name + ' ' + selected.last_name : ''"></p>
                        <p class="text-[13px] text-gray-400" x-text="selected ? selected.position : ''"></p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[11px] font-semibold border border-gray-300 text-gray-600"
                              x-text="selected ? selected.employee_code : ''"></span>
                    </div>
                </div>

                {{-- Details Grid --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide font-semibold mb-0.5">Department</p>
                        <p class="text-[13px] text-gray-800" x-text="selected?.department?.name ?? '—'"></p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide font-semibold mb-0.5">Employment Type</p>
                        <p class="text-[13px] text-gray-800 capitalize" x-text="selected ? selected.employment_type : ''"></p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide font-semibold mb-0.5">Date Started</p>
                        <p class="text-[13px] text-gray-800" x-text="selected ? formatDate(selected.date_started) : ''"></p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide font-semibold mb-0.5">Date of Birth</p>
                        <p class="text-[13px] text-gray-800" x-text="selected?.date_of_birth ? formatDate(selected.date_of_birth) : '—'"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide font-semibold mb-0.5">Email</p>
                        <p class="text-[13px] text-gray-800" x-text="selected ? selected.email : ''"></p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide font-semibold mb-0.5">Phone</p>
                        <p class="text-[13px] text-gray-800" x-text="selected?.phone_number ?? '—'"></p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide font-semibold mb-0.5">Status</p>
                        <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">Active</span>
                    </div>
                    <div class="col-span-2">
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide font-semibold mb-0.5">Address</p>
                        <p class="text-[13px] text-gray-800" x-text="selected?.address ?? '—'"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════
         MODAL: EDIT EMPLOYEE
    ═══════════════════════════════════════════════════════ --}}
    <div x-show="showEdit"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none;">

        <div class="absolute inset-0 bg-black/40" @click="showEdit = false"></div>

        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-[16px] font-bold text-gray-900">Edit Employee</h3>
                <button @click="showEdit = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>

            <form method="POST" :action="selected ? '/employees/' + selected.id : '#'" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" :value="selected?.first_name"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" :value="selected?.last_name"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Department <span class="text-red-500">*</span></label>
                        <select name="department_id" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                            @foreach($departments as $dept)
                                <option :value="'{{ $dept->id }}'"
                                        :selected="selected && selected.department_id == '{{ $dept->id }}'">
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Position <span class="text-red-500">*</span></label>
                        <input type="text" name="position" :value="selected?.position"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Employment Type <span class="text-red-500">*</span></label>
                        <select name="employment_type" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                            <option :selected="selected && selected.employment_type === 'full-time'" value="full-time">Full-time</option>
                            <option :selected="selected && selected.employment_type === 'part-time'" value="part-time">Part-time</option>
                            <option :selected="selected && selected.employment_type === 'contract'"  value="contract">Contractual</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Date Started <span class="text-red-500">*</span></label>
                        <input type="date" name="date_started" :value="selected?.date_started ? selected.date_started.substring(0,10) : ''"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" :value="selected?.email"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Phone</label>
                        <input type="text" name="phone_number" :value="selected?.phone_number"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Date of Birth</label>
                        <input type="date" name="date_of_birth" :value="selected?.date_of_birth ? selected.date_of_birth.substring(0,10) : ''"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Address</label>
                        <textarea name="address" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky resize-none"
                                  x-text="selected?.address"></textarea>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="showEdit = false"
                            class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="h-9 px-5 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════
         MODAL: TERMINATE EMPLOYEE
    ═══════════════════════════════════════════════════════ --}}
    <div x-show="showTerminate"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none;">

        <div class="absolute inset-0 bg-black/40" @click="showTerminate = false"></div>

        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-10">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-[16px] font-bold text-gray-900">Terminate Employee</h3>
                <button @click="showTerminate = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>

            <form method="POST" :action="selected ? '/employees/' + selected.id + '/terminate' : '#'" class="p-6">
                @csrf

                {{-- Employee Info Card --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 mb-5 text-[13px]">
                    <p class="text-gray-700"><span class="font-semibold">Employee ID:</span> <span x-text="selected?.id"></span></p>
                    <p class="text-gray-700 mt-1"><span class="font-semibold">Name:</span> <span x-text="selected ? selected.first_name + ' ' + selected.last_name : ''"></span></p>
                    <p class="text-gray-700 mt-1"><span class="font-semibold">Submission Date:</span> {{ now()->format('Y-m-d') }}</p>
                </div>

                {{-- Last Working Day --}}
                <div class="mb-4">
                    <label class="block text-[13px] font-semibold text-gray-800 mb-1.5">
                        Last Working Day <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="last_working_day" required
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-red-400">
                </div>

                {{-- Reason for Leaving --}}
                <div class="mb-5">
                    <label class="block text-[13px] font-semibold text-gray-800 mb-1.5">
                        Reason for Leaving <span class="text-red-500">*</span>
                    </label>
                    <textarea name="termination_reason" rows="3" required
                              placeholder="Enter reason..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-red-400 resize-none"></textarea>
                </div>

                {{-- Required Documentation --}}
                <div class="mb-4">
                    <p class="text-[13px] font-semibold text-gray-800 mb-2">
                        Required Documentation <span class="text-red-500">*</span>
                    </p>
                    <label class="flex items-center gap-2.5 mb-2 cursor-pointer">
                        <input type="checkbox" name="doc_compliance" value="1" checked
                               class="w-4 h-4 accent-sky rounded">
                        <span class="text-[13px] text-gray-700">Documentation &amp; Compliance</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="exit_interview" value="1" checked
                               class="w-4 h-4 accent-sky rounded">
                        <span class="text-[13px] text-gray-700">Exit Interview</span>
                    </label>
                </div>

                {{-- Required Paperwork --}}
                <div class="mb-6">
                    <p class="text-[13px] font-semibold text-gray-800 mb-2">
                        Required Paperwork <span class="text-red-500">*</span>
                    </p>
                    <div class="flex flex-wrap gap-x-6 gap-y-2">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="clearance_form" value="1" checked
                                   class="w-4 h-4 accent-sky rounded">
                            <span class="text-[13px] text-gray-700">Clearance Form</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="final_pay_ack" value="1" checked
                                   class="w-4 h-4 accent-sky rounded">
                            <span class="text-[13px] text-gray-700">Final Pay Acknowledgment</span>
                        </label>
                    </div>
                </div>

                <button type="submit"
                        class="w-full h-11 bg-red-600 hover:bg-red-700 text-white font-semibold text-[14px] rounded-lg transition-colors">
                    Terminate Employee
                </button>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}


@push('scripts')
<script>
function employeeManager() {
    return {
        showView:      false,
        showEdit:      false,
        showTerminate: false,
        selected:      null,

        openView(emp) {
            this.selected = emp;
            this.showView = true;
        },
        openEdit(emp) {
            this.selected = emp;
            this.showEdit = true;
        },
        openTerminate(emp) {
            this.selected = emp;
            this.showTerminate = true;
        },
        formatDate(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-GB', { day:'2-digit', month:'2-digit', year:'numeric' });
        }
    }
}
</script>
@endpush

@endsection