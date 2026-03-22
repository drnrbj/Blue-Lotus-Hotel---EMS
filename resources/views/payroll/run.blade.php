@extends('layouts.app')
@section('title', 'Payroll')
@section('subtitle', 'Process a new payroll run')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')

@include('payroll._tabs', ['active' => 'run'])

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5" x-data="runPayroll()">

    {{-- Left: Config Form --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg border border-gray-200 p-6 sticky top-6">
            <h2 class="text-[15px] font-bold text-gray-900 mb-1">Payroll Configuration</h2>
            <p class="text-[12px] text-gray-400 mb-5">Set the period and rate before processing</p>

            <form method="POST" action="{{ route('payroll.process') }}" id="payroll-form" @submit="submitting = true">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Period Start <span class="text-red-500">*</span></label>
                        <input type="date" name="period_start" x-model="periodStart"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Period End <span class="text-red-500">*</span></label>
                        <input type="date" name="period_end" x-model="periodEnd"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Hourly Rate (₱) <span class="text-red-500">*</span></label>
                        <input type="number" name="hourly_rate" step="0.01" min="1"
                               x-model="hourlyRate" placeholder="e.g. 87.50"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                        <p class="text-[11px] text-gray-400 mt-1">Applied uniformly to all selected employees</p>
                    </div>

                    {{-- Deduction Info --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-[12px] text-blue-700 space-y-1">
                        <p class="font-semibold mb-1">Auto-applied deductions:</p>
                        <p>· SSS — 4.5% of basic pay</p>
                        <p>· PhilHealth — 2.5% of basic pay</p>
                        <p>· Pag-IBIG — 2.0% of basic pay</p>
                        <p>· Withholding Tax — 5.0% of basic pay</p>
                    </div>

                    {{-- Selected count --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 flex items-center justify-between">
                        <span class="text-[13px] text-gray-600">Employees selected</span>
                        <span class="text-[15px] font-bold text-navy" x-text="selectedCount"></span>
                    </div>

                    <button type="submit" form="payroll-form"
                            :disabled="selectedCount === 0 || submitting"
                            class="w-full h-11 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90 transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <template x-if="!submitting">
                            <span>Process Payroll</span>
                        </template>
                        <template x-if="submitting">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin" width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="5.5" stroke="white" stroke-width="2" stroke-dasharray="20" stroke-dashoffset="10"/></svg>
                                Processing...
            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Right: Employee Selection --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-[15px] font-bold text-gray-900">Select Employees</h2>
                    <p class="text-[12px] text-gray-400 mt-0.5">Choose who to include in this payroll run</p>
                </div>
                <div class="flex items-center gap-3">
                    <input type="text" x-model="search" placeholder="Search name..."
                           class="h-8 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky w-44">
                    <button type="button" @click="selectAll"
                            class="h-8 px-3 border border-gray-300 rounded-lg text-[12px] text-gray-600 hover:bg-gray-50">
                        Select All
                    </button>
                    <button type="button" @click="clearAll"
                            class="h-8 px-3 border border-gray-300 rounded-lg text-[12px] text-gray-600 hover:bg-gray-50">
                        Clear
                    </button>
                </div>
            </div>

            <div class="divide-y divide-gray-50 max-h-[520px] overflow-y-auto">
                @foreach($employees as $emp)
                    <label class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 cursor-pointer transition-colors"
                           x-show="!search || '{{ strtolower($emp->full_name) }}'.includes(search.toLowerCase())">
                        <input type="checkbox"
                               name="employee_ids[]"
                               value="{{ $emp->id }}"
                               form="payroll-form"
                               @change="updateCount"
                               class="w-4 h-4 accent-navy rounded flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-sky text-[11px] font-bold flex-shrink-0">
                            {{ $emp->initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-medium text-gray-900">{{ $emp->full_name }}</p>
                            <p class="text-[11px] text-gray-400">{{ $emp->position }} · {{ $emp->department->name ?? '' }}</p>
                        </div>
                        <div class="text-right flex-shrink-0" x-show="hourlyRate > 0">
                            <p class="text-[11px] text-gray-400">Est. basic</p>
                            <p class="text-[12px] font-semibold text-gray-700">—</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function runPayroll() {
    return {
        selectedCount: 0,
        hourlyRate:    '',
        periodStart:   '',
        periodEnd:     '',
        search:        '',
        submitting:    false,

        updateCount() {
            this.selectedCount = document.querySelectorAll('input[name="employee_ids[]"]:checked').length;
        },
        selectAll() {
            document.querySelectorAll('input[name="employee_ids[]"]').forEach(cb => { cb.checked = true; });
            this.updateCount();
        },
        clearAll() {
            document.querySelectorAll('input[name="employee_ids[]"]').forEach(cb => { cb.checked = false; });
            this.updateCount();
        },
    }
}
</script>
@endpush

@endsection