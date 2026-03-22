@extends('layouts.app')
@section('title', 'Recruitment')
@section('subtitle', 'Manage applicants and their status')

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

@include('recruitment._tabs', ['active' => 'applicants'])

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Pending</p>
        <p class="text-[26px] font-bold text-amber-500">{{ $pendingCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">New applications</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Shortlisted</p>
        <p class="text-[26px] font-bold text-blue-600">{{ $shortlistedCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">For interview</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Hired</p>
        <p class="text-[26px] font-bold text-green-600">{{ $hiredCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Successfully hired</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Rejected</p>
        <p class="text-[26px] font-bold text-red-500">{{ $rejectedCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Not qualified</p>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-200" x-data="{ showAdd: false, showView: false, selected: null }">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
            <div class="flex-shrink-0">
                <h2 class="text-[15px] font-bold text-gray-900">Applicants</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">All submitted applications</p>
            </div>
            <form method="GET" action="{{ route('recruitment.applicants') }}" class="flex flex-wrap items-center gap-2 lg:ml-auto">
                <select name="job_posting_id" class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Positions</option>
                    @foreach($postings as $post)
                        <option value="{{ $post->id }}" {{ request('job_posting_id') == $post->id ? 'selected' : '' }}>{{ $post->job_title }}</option>
                    @endforeach
                </select>
                <select name="status" class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Status</option>
                    <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Pending</option>
                    <option value="shortlisted" {{ request('status') === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                    <option value="hired"       {{ request('status') === 'hired'       ? 'selected' : '' }}>Hired</option>
                    <option value="rejected"    {{ request('status') === 'rejected'    ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="h-9 px-4 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Filter</button>
                @if(request()->hasAny(['status','job_posting_id']))
                    <a href="{{ route('recruitment.applicants') }}" class="h-9 px-3 flex items-center gap-1.5 border border-gray-300 rounded-lg text-[13px] text-gray-500 hover:bg-gray-50">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg> Clear
                    </a>
                @endif
            </form>
            <button @click="showAdd = true" class="flex-shrink-0 flex items-center gap-2 h-9 px-4 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500 transition-colors">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 1v12M1 7h12" stroke="white" stroke-width="1.8" stroke-linecap="round"/></svg>
                Add Applicant
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
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Applied Position</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Job Posting</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Date Applied</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($applicants as $app)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-[13px] text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full {{ \App\Models\Applicant::avatarColor($app->id) }} flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0">
                                    {{ $app->initials }}
                                </div>
                                <div>
                                    <p class="text-[13px] font-medium text-gray-900">{{ $app->name }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $app->email ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-700">{{ $app->applied_position }}</td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-500">{{ $app->jobPosting->job_title ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-600">{{ $app->applied_date->format('M d, Y') }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $badges = [
                                    'pending'     => 'bg-amber-100 text-amber-700',
                                    'shortlisted' => 'bg-blue-100 text-blue-700',
                                    'hired'       => 'bg-green-100 text-green-700',
                                    'rejected'    => 'bg-red-100 text-red-600',
                                ];
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badges[$app->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($app->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-1.5">
                                {{-- View --}}
                                <button @click="selected = {{ $app->toJson() }}; showView = true"
                                        class="text-sky hover:text-blue-700 transition-colors" title="View">
                                    <svg width="17" height="17" viewBox="0 0 18 18" fill="none"><path d="M1.5 9s3-6 7.5-6 7.5 6 7.5 6-3 6-7.5 6-7.5-6-7.5-6z" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/></svg>
                                </button>
                                {{-- Quick status buttons --}}
                                @if($app->status === 'pending')
                                    <form method="POST" action="{{ route('recruitment.applicants.status', $app) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="shortlisted">
                                        <button type="submit" class="h-6 px-2 bg-blue-100 hover:bg-blue-200 text-blue-700 text-[10px] font-semibold rounded transition-colors">Shortlist</button>
                                    </form>
                                @endif
                                @if(in_array($app->status, ['pending','shortlisted']))
                                    <form method="POST" action="{{ route('recruitment.applicants.status', $app) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="hired">
                                        <button type="submit" class="h-6 px-2 bg-green-100 hover:bg-green-200 text-green-700 text-[10px] font-semibold rounded transition-colors">Hire</button>
                                    </form>
                                    <form method="POST" action="{{ route('recruitment.applicants.status', $app) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="h-6 px-2 bg-red-100 hover:bg-red-200 text-red-600 text-[10px] font-semibold rounded transition-colors">Reject</button>
                                    </form>
                                @endif
                                {{-- Delete --}}
                                <form method="POST" action="{{ route('recruitment.applicants.destroy', $app) }}" onsubmit="return confirm('Remove applicant?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 transition-colors">
                                        <svg width="15" height="15" viewBox="0 0 17 17" fill="none"><polyline points="3,4 4,4 14,4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M6 4V3h5v1M5 4l.8 9h5.4L12 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-14 text-center text-[13px] text-gray-400">No applicants found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applicants->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $applicants->links() }}</div>
    @endif

    {{-- MODAL: Add Applicant --}}
    <div x-show="showAdd" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showAdd = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-[16px] font-bold text-gray-900">Add Applicant</h3>
                <button @click="showAdd = false" class="text-gray-400 hover:text-gray-600"><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
            </div>
            <form method="POST" action="{{ route('recruitment.applicants.store') }}" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Email</label>
                        <input type="email" name="email" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Phone</label>
                        <input type="text" name="phone" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Applied Position <span class="text-red-500">*</span></label>
                        <input type="text" name="applied_position" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Job Posting</label>
                        <select name="job_posting_id" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">
                            <option value="">None</option>
                            @foreach($postings as $post)
                                <option value="{{ $post->id }}">{{ $post->job_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Date Applied <span class="text-red-500">*</span></label>
                        <input type="date" name="applied_date" value="{{ now()->format('Y-m-d') }}" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky resize-none"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showAdd = false" class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="h-9 px-5 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500">Add</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: View Applicant --}}
    <div x-show="showView" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showView = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-[16px] font-bold text-gray-900">Applicant Details</h3>
                <button @click="showView = false" class="text-gray-400 hover:text-gray-600"><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-sky flex items-center justify-center text-white text-[14px] font-bold">
                        <span x-text="selected ? selected.name.split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase() : ''"></span>
                    </div>
                    <div>
                        <p class="text-[15px] font-bold text-gray-900" x-text="selected?.name"></p>
                        <p class="text-[12px] text-gray-400" x-text="selected?.applied_position"></p>
                    </div>
                </div>
                <div class="space-y-2 text-[13px]">
                    <div class="flex justify-between"><span class="text-gray-400">Email</span><span class="text-gray-800" x-text="selected?.email ?? '—'"></span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Phone</span><span class="text-gray-800" x-text="selected?.phone ?? '—'"></span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Date Applied</span><span class="text-gray-800" x-text="selected?.applied_date"></span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="font-semibold capitalize" x-text="selected?.status"></span></div>
                </div>
                <template x-if="selected?.notes">
                    <div class="bg-gray-50 rounded-lg p-3 mt-2">
                        <p class="text-[11px] text-gray-400 uppercase font-semibold mb-1">Notes</p>
                        <p class="text-[13px] text-gray-700" x-text="selected.notes"></p>
                    </div>
                </template>
                <div class="flex justify-end pt-3 border-t border-gray-100">
                    <button @click="showView = false" class="h-9 px-5 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection