@extends('layouts.app')

@section('title', 'Performance Evaluation')
@section('subtitle', 'Analytics and performance insights')

@section('content')

@include('performance._tabs', ['active' => 'analytics'])

{{-- Top KPI Row --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Overall Avg Score</p>
        <p class="text-[30px] font-bold text-gray-900">{{ $overallAvg ? number_format($overallAvg, 1) . '%' : '—' }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Across {{ $completedCount }} completed evaluations</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Completed</p>
        <p class="text-[30px] font-bold text-green-600">{{ $completedCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Evaluations scored</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Pending</p>
        <p class="text-[30px] font-bold text-amber-500">{{ $pendingCount }}</p>
        <p class="text-[12px] text-gray-400 mt-1">Awaiting scoring</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Department Averages --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-[15px] font-bold text-gray-900 mb-1">Department Average Scores</h2>
        <p class="text-[12px] text-gray-400 mb-5">Average performance score per department</p>

        @forelse($deptScores as $dept)
            <div class="mb-4">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[13px] font-medium text-gray-700">{{ $dept['name'] }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-gray-400">{{ $dept['count'] }} eval{{ $dept['count'] !== 1 ? 's' : '' }}</span>
                        <span class="text-[13px] font-bold
                            {{ $dept['avg'] >= 75 ? 'text-green-600' : ($dept['avg'] >= 50 ? 'text-amber-500' : 'text-red-500') }}">
                            {{ $dept['avg'] }}%
                        </span>
                    </div>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all
                        {{ $dept['avg'] >= 75 ? 'bg-green-500' : ($dept['avg'] >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                         style="width: {{ $dept['avg'] }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-[13px] text-gray-400 py-6 text-center">No completed evaluations yet.</p>
        @endforelse
    </div>

    {{-- Rating Distribution --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-[15px] font-bold text-gray-900 mb-1">Rating Distribution</h2>
        <p class="text-[12px] text-gray-400 mb-5">Breakdown of performance ratings</p>

        @php
            $total = array_sum($distribution);
            $ratingStyles = [
                'Outstanding'          => ['bar' => 'bg-emerald-500', 'badge' => 'bg-emerald-100 text-emerald-700'],
                'Exceeds Expectations' => ['bar' => 'bg-green-500',   'badge' => 'bg-green-100 text-green-700'],
                'Meets Expectations'   => ['bar' => 'bg-blue-500',    'badge' => 'bg-blue-100 text-blue-700'],
                'Needs Improvement'    => ['bar' => 'bg-amber-400',   'badge' => 'bg-amber-100 text-amber-700'],
                'Unsatisfactory'       => ['bar' => 'bg-red-400',     'badge' => 'bg-red-100 text-red-600'],
            ];
        @endphp

        <div class="space-y-3">
            @foreach($distribution as $label => $count)
                @php
                    $pct = $total > 0 ? round(($count / $total) * 100) : 0;
                    $style = $ratingStyles[$label];
                @endphp
                <div class="flex items-center gap-3">
                    <span class="w-[148px] text-[12px] font-medium text-gray-700 flex-shrink-0">{{ $label }}</span>
                    <div class="flex-1 h-5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $style['bar'] }} rounded-full flex items-center justify-end pr-2 transition-all"
                             style="width: {{ max($pct, $count > 0 ? 8 : 0) }}%; min-width: {{ $count > 0 ? '2rem' : '0' }}">
                            @if($count > 0)
                                <span class="text-white text-[10px] font-bold">{{ $pct }}%</span>
                            @endif
                        </div>
                    </div>
                    <span class="w-6 text-right text-[12px] font-semibold text-gray-600 flex-shrink-0">{{ $count }}</span>
                </div>
            @endforeach
        </div>

        @if($total === 0)
            <p class="text-[13px] text-gray-400 py-4 text-center">No completed evaluations yet.</p>
        @endif
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Top Performers --}}
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-[15px] font-bold text-gray-900">Top Performers</h2>
            <p class="text-[12px] text-gray-400 mt-0.5">Highest scoring evaluations</p>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($topPerformers as $i => $eval)
                <div class="flex items-center gap-3 px-6 py-3.5">
                    {{-- Rank badge --}}
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold flex-shrink-0
                        {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-200 text-gray-600' : ($i === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500')) }}">
                        {{ $i + 1 }}
                    </div>
                    {{-- Avatar --}}
                    <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-sky text-[11px] font-bold flex-shrink-0">
                        {{ $eval->employee->initials ?? '??' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-medium text-gray-900 truncate">{{ $eval->employee->full_name ?? '—' }}</p>
                        <p class="text-[11px] text-gray-400">{{ $eval->employee->department->name ?? '' }} · {{ $eval->period }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-[15px] font-bold
                            {{ $eval->score >= 75 ? 'text-green-600' : ($eval->score >= 50 ? 'text-amber-500' : 'text-red-500') }}">
                            {{ number_format($eval->score, 1) }}%
                        </p>
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded {{ $eval->rating_color }}">
                            {{ $eval->rating_label }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-[13px] text-gray-400">No completed evaluations yet.</div>
            @endforelse
        </div>
    </div>

    {{-- Criteria Averages --}}
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-[15px] font-bold text-gray-900 mb-1">Criteria Averages</h2>
        <p class="text-[12px] text-gray-400 mb-5">Average rating per evaluation criterion (out of 5)</p>

        @if(collect($criteriaAverages)->sum() > 0)
            <div class="space-y-4">
                @foreach($criteriaAverages as $label => $avg)
                    <div>
                        <div class="flex justify-between mb-1.5">
                            <span class="text-[13px] font-medium text-gray-700">{{ $label }}</span>
                            <span class="text-[13px] font-bold
                                {{ $avg >= 4 ? 'text-green-600' : ($avg >= 3 ? 'text-blue-600' : ($avg >= 2 ? 'text-amber-500' : 'text-red-500')) }}">
                                {{ number_format($avg, 2) }} / 5
                            </span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full
                                {{ $avg >= 4 ? 'bg-green-500' : ($avg >= 3 ? 'bg-blue-500' : ($avg >= 2 ? 'bg-amber-400' : 'bg-red-400')) }}"
                                 style="width: {{ ($avg / 5) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-[13px] text-gray-400 py-6 text-center">No criteria data available yet.</p>
        @endif
    </div>

</div>

@endsection