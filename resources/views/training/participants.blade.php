@extends('layouts.app')
@section('title', 'Training')
@section('subtitle', 'Manage training enrollments and completion')

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

@include('training._tabs', ['active' => 'participants'])

{{-- Stats --}}
<div class="grid grid-cols-2 gap-4 mb-5">
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Ongoing</p>
        <p class="text-[26px] font-bold text-amber-500">{{ $ongoingCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Currently enrolled</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Completed</p>
        <p class="text-[26px] font-bold text-green-600">{{ $completedCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Finished training</p>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-200" x-data="{ showEnroll: false }">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
            <div class="flex-shrink-0">
                <h2 class="text-[15px] font-bold text-gray-900">Participants</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">Employee training enrollments</p>
            </div>
            <form method="GET" action="{{ route('training.participants') }}" class="flex flex-wrap items-center gap-2 lg:ml-auto">
                <select name="training_id" class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Programs</option>
                    @foreach($allPrograms as $prog)
                        <option value="{{ $prog->id }}" {{ request('training_id') == $prog->id ? 'selected' : '' }}>{{ $prog->program_name }}</option>
                    @endforeach
                </select>
                <select name="status" class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Status</option>
                    <option value="ongoing"   {{ request('status') === 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <button type="submit" class="h-9 px-4 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Filter</button>
                @if(request()->hasAny(['training_id','status','department_id']))
                    <a href="{{ route('training.participants') }}" class="h-9 px-3 flex items-center gap-1.5 border border-gray-300 rounded-lg text-[13px] text-gray-500 hover:bg-gray-50">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg> Clear
                    </a>
                @endif
            </form>
            <button @click="showEnroll = true" class="flex-shrink-0 flex items-center gap-2 h-9 px-4 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500 transition-colors">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 1v12M1 7h12" stroke="white" stroke-width="1.8" stroke-linecap="round"/></svg>
                Enroll Employees
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
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Training Program</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Duration</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($participants as $p)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-[13px] text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-sky text-[11px] font-bold flex-shrink-0">
                                    {{ $p->employee->initials ?? '??' }}
                                </div>
                                <div>
                                    <p class="text-[13px] font-medium text-gray-900">{{ $p->employee->full_name ?? '—' }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $p->employee->position ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-500">{{ $p->employee->department->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <p class="text-[13px] font-medium text-gray-800">{{ $p->training->program_name ?? '—' }}</p>
                            <p class="text-[11px] text-gray-400">{{ $p->training->department->name ?? 'All Departments' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-600">
                            {{ $p->training ? $p->training->start_date->format('M d') . ' – ' . $p->training->end_date->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($p->status === 'ongoing')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Ongoing
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Completed
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                @if($p->status === 'ongoing')
                                    <form method="POST" action="{{ route('training.participants.complete', $p) }}">
                                        @csrf
                                        <button type="submit" class="h-6 px-2 bg-green-100 hover:bg-green-200 text-green-700 text-[10px] font-semibold rounded transition-colors">Mark Complete</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('training.participants.remove', $p) }}" onsubmit="return confirm('Remove from training?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 transition-colors">
                                        <svg width="15" height="15" viewBox="0 0 17 17" fill="none"><polyline points="3,4 4,4 14,4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M6 4V3h5v1M5 4l.8 9h5.4L12 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-14 text-center text-[13px] text-gray-400">No participants yet. Click <strong>Enroll Employees</strong> to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($participants->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $participants->links() }}</div>
    @endif

    {{-- MODAL: Enroll --}}
    <div x-show="showEnroll" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showEnroll = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-[16px] font-bold text-gray-900">Enroll Employees</h3>
                    <p class="text-[12px] text-gray-400 mt-0.5">Select a program and employees to enroll</p>
                </div>
                <button @click="showEnroll = false" class="text-gray-400 hover:text-gray-600"><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
            </div>
            <form method="POST" action="{{ route('training.enroll') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Training Program <span class="text-red-500">*</span></label>
                    <select name="training_id" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                        <option value="">Select program...</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}" {{ request('training_id') == $prog->id ? 'selected' : '' }}>
                                {{ $prog->program_name }} ({{ ucfirst($prog->status) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Employees <span class="text-red-500">*</span>
                        <span class="text-gray-400 normal-case font-normal">(hold Ctrl/Cmd to select multiple)</span>
                    </label>
                    <select name="employee_ids[]" multiple size="8"
                            class="w-full border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky"
                            style="padding: 4px;" required>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" class="py-1.5 px-3 hover:bg-blue-50 rounded">
                                {{ $emp->full_name }} — {{ $emp->department->name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showEnroll = false" class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="h-9 px-5 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500">Enroll</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection