@extends('layouts.app')

@section('title', 'Attendance')
@section('subtitle', 'Employee work schedules and shift assignments')

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

@include('attendance._tabs', ['active' => 'schedules'])

<div class="bg-white rounded-lg border border-gray-200" x-data="scheduleManager()">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">

            <div class="flex-shrink-0">
                <h2 class="text-[15px] font-bold text-gray-900">Employee Schedules</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">Active shift assignments per employee</p>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('attendance.schedules') }}"
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

                <button type="submit"
                        class="h-9 px-4 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90 transition-colors">
                    Filter
                </button>

                @if(request()->hasAny(['department_id','employee_id']))
                    <a href="{{ route('attendance.schedules') }}"
                       class="h-9 px-3 flex items-center gap-1.5 border border-gray-300 rounded-lg text-[13px] text-gray-500 hover:bg-gray-50">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        Clear
                    </a>
                @endif

            </form>

            {{-- Assign Schedule Button --}}
            <button type="button" @click="showAssign = true"
                    class="flex-shrink-0 flex items-center gap-2 h-9 px-4 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500 transition-colors">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M7 1v12M1 7h12" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Assign Schedule
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
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Department</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Shift</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Work Days</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Effective Date</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($schedules as $sched)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-[13px] text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-sky text-[11px] font-bold flex-shrink-0">
                                    {{ $sched->employee->initials ?? '??' }}
                                </div>
                                <div>
                                    <p class="text-[13px] font-medium text-gray-900">{{ $sched->employee->full_name ?? '—' }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $sched->employee->position ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-500">{{ $sched->employee->department->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <p class="text-[13px] font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($sched->shift_start)->format('h:i A') }}
                                –
                                {{ \Carbon\Carbon::parse($sched->shift_end)->format('h:i A') }}
                            </p>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex flex-wrap gap-1">
                                @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                                    @php $active = in_array($day, explode(',', $sched->days)); @endphp
                                    <span class="inline-block w-8 text-center py-0.5 rounded text-[10px] font-semibold
                                                 {{ $active ? 'bg-navy text-white' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $day }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-600">
                            {{ $sched->effective_date->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3.5">
                            <button type="button"
                                @click="openEdit({{ $sched->toJson() }})"
                                class="text-green-500 hover:text-green-700 transition-colors" title="Edit">
                                <svg width="16" height="16" viewBox="0 0 17 17" fill="none">
                                    <path d="M11.5 2.5l3 3L5 15H2v-3L11.5 2.5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-14 text-center text-[13px] text-gray-400">
                            No schedules assigned yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($schedules->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $schedules->links() }}</div>
    @endif


    {{-- ── MODAL: Assign Schedule ── --}}
    <div x-show="showAssign"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showAssign = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-[16px] font-bold text-gray-900">Assign Schedule</h3>
                <button @click="showAssign = false" class="text-gray-400 hover:text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('attendance.schedules.store') }}" class="p-6 space-y-4">
                @csrf
                @include('attendance._schedule-form', ['employees' => $employees, 'sched' => null])
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showAssign = false"
                            class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit"
                            class="h-9 px-5 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Assign</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL: Edit Schedule ── --}}
    <div x-show="showEdit"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showEdit = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-[16px] font-bold text-gray-900">Edit Schedule</h3>
                <button @click="showEdit = false" class="text-gray-400 hover:text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>
            <form method="POST" :action="selected ? '/attendance/schedules/' + selected.id : '#'" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Employee</label>
                    <p class="text-[13px] text-gray-800 font-medium" x-text="selected ? selected.employee?.first_name + ' ' + selected.employee?.last_name : ''"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Shift Start <span class="text-red-500">*</span></label>
                        <input type="time" name="shift_start" :value="selected?.shift_start"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Shift End <span class="text-red-500">*</span></label>
                        <input type="time" name="shift_end" :value="selected?.shift_end"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-2">Work Days <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="days[]" value="{{ $day }}"
                                       :checked="selected && selected.days && selected.days.includes('{{ $day }}')"
                                       class="sr-only peer">
                                <span class="inline-block w-10 text-center py-1.5 rounded border text-[12px] font-semibold
                                             border-gray-300 text-gray-500
                                             peer-checked:bg-navy peer-checked:text-white peer-checked:border-navy
                                             hover:border-navy/50 transition-colors cursor-pointer">
                                    {{ $day }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Effective Date <span class="text-red-500">*</span></label>
                    <input type="date" name="effective_date" :value="selected?.effective_date ? selected.effective_date.substring(0,10) : ''"
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showEdit = false"
                            class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit"
                            class="h-9 px-5 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function scheduleManager() {
    return {
        showAssign: false,
        showEdit:   false,
        selected:   null,
        openEdit(sched) {
            this.selected  = sched;
            this.showEdit  = true;
        }
    }
}
</script>
@endpush

@endsection