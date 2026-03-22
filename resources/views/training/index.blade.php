@extends('layouts.app')
@section('title', 'Training')
@section('subtitle', 'Manage employee training and development programs')

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

@include('training._tabs', ['active' => 'programs'])

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Upcoming</p>
        <p class="text-[26px] font-bold text-blue-600">{{ $upcomingCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Not yet started</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Ongoing</p>
        <p class="text-[26px] font-bold text-amber-500">{{ $ongoingCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Currently running</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Completed</p>
        <p class="text-[26px] font-bold text-green-600">{{ $completedCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Finished</p>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-200" x-data="trainingManager()">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
            <div class="flex-shrink-0">
                <h2 class="text-[15px] font-bold text-gray-900">Training Programs</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">All training and development programs</p>
            </div>
            <form method="GET" action="{{ route('training.index') }}" class="flex flex-wrap items-center gap-2 lg:ml-auto">
                <select name="department_id" class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Status</option>
                    <option value="upcoming"  {{ request('status') === 'upcoming'  ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing"   {{ request('status') === 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <button type="submit" class="h-9 px-4 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Filter</button>
                @if(request()->hasAny(['department_id','status']))
                    <a href="{{ route('training.index') }}" class="h-9 px-3 flex items-center gap-1.5 border border-gray-300 rounded-lg text-[13px] text-gray-500 hover:bg-gray-50">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg> Clear
                    </a>
                @endif
            </form>
            <button @click="showCreate = true" class="flex-shrink-0 flex items-center gap-2 h-9 px-4 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500 transition-colors">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 1v12M1 7h12" stroke="white" stroke-width="1.8" stroke-linecap="round"/></svg>
                New Program
            </button>
        </div>
    </div>

    {{-- Cards --}}
    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($programs as $prog)
            <div class="border border-gray-200 rounded-lg p-5 hover:border-sky/50 transition-colors flex flex-col gap-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[14px] font-bold text-gray-900 truncate">{{ $prog->program_name }}</h3>
                        <p class="text-[12px] text-gray-400 mt-0.5">{{ $prog->department->name ?? 'All Departments' }}</p>
                    </div>
                    @if($prog->status === 'upcoming')
                        <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-700">Upcoming</span>
                    @elseif($prog->status === 'ongoing')
                        <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700">Ongoing</span>
                    @else
                        <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">Completed</span>
                    @endif
                </div>

                @if($prog->description)
                    <p class="text-[12px] text-gray-500 leading-relaxed line-clamp-2">{{ $prog->description }}</p>
                @endif

                <div class="grid grid-cols-3 gap-2 py-2 border-y border-gray-100 text-center">
                    <div>
                        <p class="text-[13px] font-semibold text-gray-700">{{ $prog->start_date->format('M d') }}</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Start</p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-gray-700">{{ $prog->end_date->format('M d') }}</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide">End</p>
                    </div>
                    <div>
                        <p class="text-[13px] font-bold text-gray-900">{{ $prog->participants_count }}</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Enrolled</p>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-[11px] text-gray-400">{{ $prog->duration }}</span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('training.participants') }}?training_id={{ $prog->id }}"
                           class="text-sky text-[11px] font-semibold hover:underline">Participants →</a>
                        <button @click="openEdit({{ $prog->toJson() }})" class="text-green-500 hover:text-green-700 transition-colors" title="Edit">
                            <svg width="15" height="15" viewBox="0 0 17 17" fill="none"><path d="M11.5 2.5l3 3L5 15H2v-3L11.5 2.5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                        </button>
                        <form method="POST" action="{{ route('training.destroy', $prog) }}" onsubmit="return confirm('Delete this program?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 transition-colors">
                                <svg width="15" height="15" viewBox="0 0 17 17" fill="none"><polyline points="3,4 4,4 14,4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M6 4V3h5v1M5 4l.8 9h5.4L12 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-16 text-center text-[13px] text-gray-400">
                No training programs found. Click <strong>New Program</strong> to create one.
            </div>
        @endforelse
    </div>

    @if($programs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $programs->links() }}</div>
    @endif

    {{-- MODAL: Create Program --}}
    <div x-show="showCreate" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showCreate = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-[16px] font-bold text-gray-900">New Training Program</h3>
                <button @click="showCreate = false" class="text-gray-400 hover:text-gray-600"><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
            </div>
            <form method="POST" action="{{ route('training.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Program Name <span class="text-red-500">*</span></label>
                    <input type="text" name="program_name" placeholder="e.g. Customer Service Excellence" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Department</label>
                    <select name="department_id" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">End Date <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Description</label>
                    <textarea name="description" rows="3" placeholder="Objectives, topics covered..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showCreate = false" class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="h-9 px-5 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500">Create</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Edit Program --}}
    <div x-show="showEdit" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showEdit = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-[16px] font-bold text-gray-900">Edit Program</h3>
                <button @click="showEdit = false" class="text-gray-400 hover:text-gray-600"><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
            </div>
            <form method="POST" :action="selected ? '/training/' + selected.id : '#'" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Program Name <span class="text-red-500">*</span></label>
                    <input type="text" name="program_name" :value="selected?.program_name" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Department</label>
                        <select name="department_id" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option :value="'{{ $dept->id }}'" :selected="selected && selected.department_id == '{{ $dept->id }}'">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Status</label>
                        <select name="status" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">
                            <option :selected="selected && selected.status === 'upcoming'"  value="upcoming">Upcoming</option>
                            <option :selected="selected && selected.status === 'ongoing'"   value="ongoing">Ongoing</option>
                            <option :selected="selected && selected.status === 'completed'" value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Start Date</label>
                        <input type="date" name="start_date" :value="selected?.start_date ? selected.start_date.substring(0,10) : ''" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">End Date</label>
                        <input type="date" name="end_date" :value="selected?.end_date ? selected.end_date.substring(0,10) : ''" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky resize-none" x-text="selected?.description"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showEdit = false" class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="h-9 px-5 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Save</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function trainingManager() {
    return { showCreate: false, showEdit: false, selected: null,
             openEdit(p) { this.selected = p; this.showEdit = true; } }
}
</script>
@endpush
@endsection