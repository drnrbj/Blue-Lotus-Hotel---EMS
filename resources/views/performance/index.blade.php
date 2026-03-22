@extends('layouts.app')

@section('title', 'Performance Evaluation')
@section('subtitle', 'Track and manage employee performance assessments')

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
@if($errors->has('duplicate'))
    <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
        {{ $errors->first('duplicate') }}
    </div>
@endif

@include('performance._tabs', ['active' => 'list'])

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Total</p>
        <p class="text-[26px] font-bold text-gray-900">{{ $totalCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Evaluations</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Pending</p>
        <p class="text-[26px] font-bold text-amber-500">{{ $pendingCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Awaiting scoring</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Completed</p>
        <p class="text-[26px] font-bold text-green-600">{{ $completedCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Scored</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Avg Score</p>
        <p class="text-[26px] font-bold text-sky">{{ $avgScore ? number_format($avgScore, 1) . '%' : '—' }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Overall average</p>
    </div>
</div>

{{-- Main Table Card --}}
<div class="bg-white rounded-lg border border-gray-200" x-data="evalManager()">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">

            <div class="flex-shrink-0">
                <h2 class="text-[15px] font-bold text-gray-900">Evaluation List</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">All employee performance assessments</p>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('performance.index') }}"
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

                <select name="period"
                    class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none"
                    style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Periods</option>
                    @foreach($periods as $p)
                        <option value="{{ $p }}" {{ request('period') === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>

                <select name="status"
                    class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none"
                    style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Status</option>
                    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>

                <button type="submit"
                        class="h-9 px-4 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90 transition-colors">
                    Filter
                </button>

                @if(request()->hasAny(['department_id','period','status','employee_id']))
                    <a href="{{ route('performance.index') }}"
                       class="h-9 px-3 flex items-center gap-1.5 border border-gray-300 rounded-lg text-[13px] text-gray-500 hover:bg-gray-50">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        Clear
                    </a>
                @endif

            </form>

            {{-- New Evaluation --}}
            <button type="button" @click="showCreate = true"
                    class="flex-shrink-0 flex items-center gap-2 h-9 px-4 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500 transition-colors">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M7 1v12M1 7h12" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                New Evaluation
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
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Period</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Evaluator</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Score</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($evaluations as $eval)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-[13px] text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-sky text-[11px] font-bold flex-shrink-0">
                                    {{ $eval->employee->initials ?? '??' }}
                                </div>
                                <div>
                                    <p class="text-[13px] font-medium text-gray-900">{{ $eval->employee->full_name ?? '—' }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $eval->employee->position ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-500">{{ $eval->employee->department->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-navy/10 text-navy">
                                {{ $eval->period }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-600">{{ $eval->evaluator->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            @if($eval->status === 'completed')
                                <div class="flex items-center gap-2">
                                    {{-- Score bar --}}
                                    <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full
                                            {{ $eval->score >= 75 ? 'bg-green-500' : ($eval->score >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                                             style="width: {{ $eval->score }}%"></div>
                                    </div>
                                    <span class="text-[13px] font-semibold text-gray-800">{{ number_format($eval->score, 1) }}%</span>
                                </div>
                            @else
                                <span class="text-[13px] text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($eval->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Completed
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                {{-- View (completed only) --}}
                                @if($eval->status === 'completed')
                                    <button type="button" title="View Results"
                                        @click="openView({{ $eval->toJson() }})"
                                        class="text-sky hover:text-blue-700 transition-colors">
                                        <svg width="17" height="17" viewBox="0 0 18 18" fill="none">
                                            <path d="M1.5 9s3-6 7.5-6 7.5 6 7.5 6-3 6-7.5 6-7.5-6-7.5-6z" stroke="currentColor" stroke-width="1.5"/>
                                            <circle cx="9" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- Score (pending only) --}}
                                @if($eval->status === 'pending')
                                    <button type="button" title="Score Evaluation"
                                        @click="openScore({{ $eval->toJson() }})"
                                        class="text-emerald-500 hover:text-emerald-700 transition-colors">
                                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                                            <path d="M8.5 2l1.8 3.5 4 .6-2.9 2.8.7 4L8.5 11l-3.6 1.9.7-4L2.7 6.1l4-.6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('performance.destroy', $eval) }}"
                                      onsubmit="return confirm('Delete this evaluation?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Delete"
                                            class="text-red-400 hover:text-red-600 transition-colors">
                                        <svg width="16" height="16" viewBox="0 0 17 17" fill="none">
                                            <polyline points="3,4 4,4 14,4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M6 4V3h5v1M5 4l.8 9h5.4L12 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-[13px] text-gray-400">
                            No evaluations found. Click <strong>New Evaluation</strong> to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($evaluations->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $evaluations->links() }}</div>
    @endif


    {{-- ═══════════════════════════════════════════
         MODAL: Create Evaluation
    ═══════════════════════════════════════════ --}}
    <div x-show="showCreate"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showCreate = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-[16px] font-bold text-gray-900">New Evaluation</h3>
                    <p class="text-[12px] text-gray-400 mt-0.5">Assign a performance evaluation to an employee</p>
                </div>
                <button @click="showCreate = false" class="text-gray-400 hover:text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('performance.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id"
                            class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                        <option value="">Select employee...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} — {{ $emp->department->name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        Evaluation Period <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="period" placeholder="e.g. Q1 2025, Annual 2025"
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    <p class="text-[11px] text-gray-400 mt-1">Format: Q1 2025 · Q2 2025 · Annual 2025</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-[12px] text-blue-700">
                    <strong>Note:</strong> The evaluation will be assigned to you as evaluator. You can score it after creation.
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showCreate = false"
                            class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit"
                            class="h-9 px-5 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500">Create</button>
                </div>
            </form>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════
         MODAL: Score Evaluation
    ═══════════════════════════════════════════ --}}
    <div x-show="showScore"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showScore = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-[16px] font-bold text-gray-900">Score Evaluation</h3>
                    <p class="text-[12px] text-gray-400 mt-0.5">
                        <span x-text="selected ? selected.employee?.first_name + ' ' + selected.employee?.last_name : ''"></span>
                        &nbsp;·&nbsp;
                        <span x-text="selected?.period"></span>
                    </p>
                </div>
                <button @click="showScore = false" class="text-gray-400 hover:text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>
            <form method="POST" :action="selected ? '/performance/' + selected.id + '/score' : '#'" class="p-6">
                @csrf

                <p class="text-[12px] text-gray-500 mb-5">
                    Rate each criterion from <strong>1</strong> (Poor) to <strong>5</strong> (Excellent).
                    The final score is calculated automatically.
                </p>

                {{-- Criteria Ratings --}}
                <div class="space-y-5 mb-6">
                    @foreach(\App\Http\Controllers\EvaluationController::CRITERIA as $key => $label)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-[13px] font-semibold text-gray-700">{{ $label }}</label>
                                <span class="text-[11px] text-gray-400">1 = Poor &nbsp;·&nbsp; 5 = Excellent</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @foreach([1,2,3,4,5] as $val)
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="criteria[{{ $key }}]" value="{{ $val }}"
                                               class="sr-only peer" required>
                                        <div class="flex items-center justify-center h-9 rounded-lg border border-gray-200 text-[13px] font-semibold text-gray-500
                                                    peer-checked:bg-navy peer-checked:text-white peer-checked:border-navy
                                                    hover:border-navy/50 transition-colors">
                                            {{ $val }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Remarks --}}
                <div class="mb-6">
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Remarks / Comments</label>
                    <textarea name="remarks" rows="3" placeholder="Optional overall feedback..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showScore = false"
                            class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit"
                            class="h-9 px-5 bg-emerald-500 hover:bg-emerald-600 text-white text-[13px] font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                        <svg width="14" height="14" viewBox="0 0 17 17" fill="none">
                            <path d="M8.5 2l1.8 3.5 4 .6-2.9 2.8.7 4L8.5 11l-3.6 1.9.7-4L2.7 6.1l4-.6z" stroke="white" stroke-width="1.4" stroke-linejoin="round"/>
                        </svg>
                        Submit Score
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════
         MODAL: View Evaluation Results
    ═══════════════════════════════════════════ --}}
    <div x-show="showView"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showView = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-[16px] font-bold text-gray-900">Evaluation Results</h3>
                <button @click="showView = false" class="text-gray-400 hover:text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>
            <div class="p-6">
                {{-- Employee Info --}}
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-full bg-navy flex items-center justify-center text-sky text-[13px] font-bold flex-shrink-0"
                         x-text="selected ? (selected.employee?.first_name?.[0] ?? '') + (selected.employee?.last_name?.[0] ?? '') : ''"></div>
                    <div>
                        <p class="text-[15px] font-bold text-gray-900"
                           x-text="selected ? selected.employee?.first_name + ' ' + selected.employee?.last_name : ''"></p>
                        <p class="text-[12px] text-gray-400" x-text="selected?.employee?.position ?? ''"></p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="text-[11px] text-gray-400">Period</p>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-navy/10 text-navy"
                              x-text="selected?.period"></span>
                    </div>
                </div>

                {{-- Overall Score --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide font-semibold">Overall Score</p>
                        <p class="text-[32px] font-bold text-gray-900 mt-1" x-text="selected?.score ? parseFloat(selected.score).toFixed(1) + '%' : '—'"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide font-semibold mb-1">Rating</p>
                        <span class="inline-block px-3 py-1 rounded-full text-[12px] font-bold"
                              :class="getRatingColor(selected?.score)"
                              x-text="getRatingLabel(selected?.score)"></span>
                    </div>
                </div>

                {{-- Criteria Breakdown --}}
                <div class="mb-5">
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-3">Criteria Breakdown</p>
                    @php $criteria = \App\Http\Controllers\EvaluationController::CRITERIA; @endphp
                    <template x-if="selected?.criteria">
                        <div class="space-y-3">
                            @foreach($criteria as $key => $label)
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-[12px] text-gray-700">{{ $label }}</span>
                                        <span class="text-[12px] font-semibold text-gray-800"
                                              x-text="selected.criteria['{{ $key }}'] ? selected.criteria['{{ $key }}'] + ' / 5' : '—'"></span>
                                    </div>
                                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-sky rounded-full transition-all"
                                             :style="'width:' + (selected.criteria['{{ $key }}'] ? (selected.criteria['{{ $key }}'] / 5 * 100) : 0) + '%'"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </template>
                    <template x-if="!selected?.criteria">
                        <p class="text-[13px] text-gray-400">No criteria data available.</p>
                    </template>
                </div>

                {{-- Remarks --}}
                <template x-if="selected?.remarks">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Remarks</p>
                        <p class="text-[13px] text-gray-700" x-text="selected.remarks"></p>
                    </div>
                </template>

                <div class="flex justify-end mt-5 pt-4 border-t border-gray-100">
                    <button type="button" @click="showView = false"
                            class="h-9 px-5 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>{{-- end x-data --}}

@push('scripts')
<script>
function evalManager() {
    return {
        showCreate: false,
        showScore:  false,
        showView:   false,
        selected:   null,

        openScore(eval_) { this.selected = eval_; this.showScore = true; },
        openView(eval_)  { this.selected = eval_; this.showView  = true; },

        getRatingLabel(score) {
            if (!score) return '—';
            if (score >= 90) return 'Outstanding';
            if (score >= 75) return 'Exceeds Expectations';
            if (score >= 60) return 'Meets Expectations';
            if (score >= 45) return 'Needs Improvement';
            return 'Unsatisfactory';
        },
        getRatingColor(score) {
            if (!score) return 'bg-gray-100 text-gray-500';
            if (score >= 90) return 'bg-emerald-100 text-emerald-700';
            if (score >= 75) return 'bg-green-100 text-green-700';
            if (score >= 60) return 'bg-blue-100 text-blue-700';
            if (score >= 45) return 'bg-amber-100 text-amber-700';
            return 'bg-red-100 text-red-600';
        }
    }
}
</script>
@endpush

@endsection