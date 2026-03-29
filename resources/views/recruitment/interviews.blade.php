@extends('layouts.app')
@section('title', 'Recruitment')
@section('subtitle', 'Schedule and manage applicant interviews')

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

@include('recruitment._tabs', ['active' => 'interviews'])

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Scheduled</p>
        <p class="text-[26px] font-bold text-sky">{{ $scheduledCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Upcoming interviews</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Done</p>
        <p class="text-[26px] font-bold text-green-600">{{ $doneCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Completed</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Cancelled</p>
        <p class="text-[26px] font-bold text-red-500">{{ $cancelledCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Cancelled</p>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-200" x-data="{ showSchedule: false }">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
            <div class="flex-shrink-0">
                <h2 class="text-[15px] font-bold text-gray-900">Interview Schedule</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">All scheduled and past interviews</p>
            </div>
            <form method="GET" action="{{ route('recruitment.interviews') }}" class="flex flex-wrap items-center gap-2 lg:ml-auto">
                <select name="status" class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Status</option>
                    <option value="scheduled"  {{ request('status') === 'scheduled'  ? 'selected' : '' }}>Scheduled</option>
                    <option value="done"       {{ request('status') === 'done'       ? 'selected' : '' }}>Done</option>
                    <option value="cancelled"  {{ request('status') === 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="h-9 px-4 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Filter</button>
                @if(request()->filled('status'))
                    <a href="{{ route('recruitment.interviews') }}" class="h-9 px-3 flex items-center gap-1.5 border border-gray-300 rounded-lg text-[13px] text-gray-500 hover:bg-gray-50">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg> Clear
                    </a>
                @endif
            </form>
            <button @click="showSchedule = true" class="flex-shrink-0 flex items-center gap-2 h-9 px-4 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500 transition-colors">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 1v12M1 7h12" stroke="white" stroke-width="1.8" stroke-linecap="round"/></svg>
                Schedule Interview
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">#</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Applicant</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Position</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Interviewer</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Schedule</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($interviews as $iv)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-[13px] text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full {{ \App\Models\Applicant::avatarColor($iv->applicant_id) }} flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0">
                                    {{ $iv->applicant->initials ?? '??' }}
                                </div>
                                <p class="text-[13px] font-medium text-gray-900">{{ $iv->applicant->name ?? '—' }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-600">{{ $iv->applicant->applied_position ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-600">{{ $iv->interviewer->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <p class="text-[13px] font-medium text-gray-800">{{ $iv->schedule_date->format('M d, Y') }}</p>
                            <p class="text-[11px] text-gray-400">{{ $iv->schedule_date->format('h:i A') }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($iv->status === 'scheduled')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky/10 text-sky">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky"></span> Scheduled
                                </span>
                            @elseif($iv->status === 'done')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Done
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Cancelled
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($iv->status === 'scheduled')
                                <div class="flex items-center gap-1.5">
                                    <form method="POST" action="{{ route('recruitment.interviews.status', $iv) }}">
                                        @csrf <input type="hidden" name="status" value="done">
                                        <button type="submit" class="h-6 px-2 bg-green-100 hover:bg-green-200 text-green-700 text-[10px] font-semibold rounded transition-colors">Done</button>
                                    </form>
                                    <form method="POST" action="{{ route('recruitment.interviews.status', $iv) }}">
                                        @csrf <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="h-6 px-2 bg-red-100 hover:bg-red-200 text-red-600 text-[10px] font-semibold rounded transition-colors">Cancel</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-[12px] text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-14 text-center text-[13px] text-gray-400">No interviews scheduled yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($interviews->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $interviews->links() }}</div>
    @endif

    {{-- MODAL: Schedule Interview --}}
    <div x-show="showSchedule" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showSchedule = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-[16px] font-bold text-gray-900">Schedule Interview</h3>
                <button @click="showSchedule = false" class="text-gray-400 hover:text-gray-600"><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
            </div>
            <form method="POST" action="{{ route('recruitment.interviews.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Applicant <span class="text-red-500">*</span></label>
                    <select name="applicant_id" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                        <option value="">Select applicant...</option>
                        @foreach($applicants as $app)
                            <option value="{{ $app->id }}">{{ $app->name }} — {{ $app->applied_position }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Interviewer <span class="text-red-500">*</span></label>
                    <select name="interviewer_id" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                        <option value="">Select interviewer...</option>
                        @foreach($interviewers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Date & Time <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="schedule_date" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Notes</label>
                    <textarea name="notes" rows="2" placeholder="Location, format, topics..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showSchedule = false" class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="h-9 px-5 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500">Schedule</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection