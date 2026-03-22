@extends('layouts.app')
@section('title', 'Payroll')
@section('subtitle', 'Employee payroll management and payslips')

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

@include('payroll._tabs', ['active' => 'list'])

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Total Gross Pay</p>
        <p class="text-[22px] font-bold text-gray-900">₱{{ number_format($totalGross, 2) }}</p>
        <p class="text-[12px] text-gray-400 mt-1">All periods</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Total Net Pay</p>
        <p class="text-[22px] font-bold text-green-600">₱{{ number_format($totalNet, 2) }}</p>
        <p class="text-[12px] text-gray-400 mt-1">After deductions</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Total Deductions</p>
        <p class="text-[22px] font-bold text-red-500">₱{{ number_format($totalDeduct, 2) }}</p>
        <p class="text-[12px] text-gray-400 mt-1">SSS, PhilHealth, etc.</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Draft / Released</p>
        <p class="text-[22px] font-bold text-gray-900">{{ $draftCount }} <span class="text-gray-400 text-[16px]">/ {{ $releasedCount }}</span></p>
        <p class="text-[12px] text-gray-400 mt-1">Pending release</p>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-200" x-data="payrollManager()">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
            <div class="flex-shrink-0">
                <h2 class="text-[15px] font-bold text-gray-900">Payroll Records</h2>
                <p class="text-[12px] text-gray-400 mt-0.5">All employee payroll entries</p>
            </div>

            <form method="GET" action="{{ route('payroll.index') }}" class="flex flex-wrap items-center gap-2 lg:ml-auto">
                <select name="period" class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Periods</option>
                    @foreach($periods as $p)
                        <option value="{{ $p['key'] }}" {{ request('period') === $p['key'] ? 'selected' : '' }}>{{ $p['label'] }}</option>
                    @endforeach
                </select>
                <select name="department_id" class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] bg-white focus:outline-none focus:border-sky appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Status</option>
                    <option value="draft"    {{ request('status') === 'draft'    ? 'selected' : '' }}>Draft</option>
                    <option value="released" {{ request('status') === 'released' ? 'selected' : '' }}>Released</option>
                </select>
                <button type="submit" class="h-9 px-4 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Filter</button>
                @if(request()->hasAny(['period','department_id','status']))
                    <a href="{{ route('payroll.index') }}" class="h-9 px-3 flex items-center gap-1.5 border border-gray-300 rounded-lg text-[13px] text-gray-500 hover:bg-gray-50">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg> Clear
                    </a>
                @endif
            </form>

            <a href="{{ route('payroll.run') }}" class="flex-shrink-0 flex items-center gap-2 h-9 px-4 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500 transition-colors">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 1v12M1 7h12" stroke="white" stroke-width="1.8" stroke-linecap="round"/></svg>
                Run Payroll
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">#</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Employee</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Period</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Hours</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Gross Pay</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Deductions</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Net Pay</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payrolls as $pay)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-[13px] text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-sky text-[11px] font-bold flex-shrink-0">
                                    {{ $pay->employee->initials ?? '??' }}
                                </div>
                                <div>
                                    <p class="text-[13px] font-medium text-gray-900">{{ $pay->employee->full_name ?? '—' }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $pay->employee->department->name ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-700">{{ $pay->period_label }}</td>
                        <td class="px-5 py-3.5 text-[13px] text-gray-700 text-right">{{ number_format($pay->total_hours, 1) }}h</td>
                        <td class="px-5 py-3.5 text-[13px] font-medium text-gray-800 text-right">₱{{ number_format($pay->gross_pay, 2) }}</td>
                        <td class="px-5 py-3.5 text-[13px] text-red-500 text-right">₱{{ number_format($pay->deductions, 2) }}</td>
                        <td class="px-5 py-3.5 text-[13px] font-bold text-green-700 text-right">₱{{ number_format($pay->net_pay, 2) }}</td>
                        <td class="px-5 py-3.5">
                            @if($pay->status === 'released')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Released
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                {{-- View Payslip --}}
                                <button @click="openPayslip({{ $pay->load('adjustments')->toJson() }})"
                                        class="text-sky hover:text-blue-700 transition-colors" title="View Payslip">
                                    <svg width="17" height="17" viewBox="0 0 18 18" fill="none"><path d="M1.5 9s3-6 7.5-6 7.5 6 7.5 6-3 6-7.5 6-7.5-6-7.5-6z" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/></svg>
                                </button>
                                {{-- Add Adjustment --}}
                                @if($pay->status === 'draft')
                                    <button @click="openAdjust({{ $pay->toJson() }})"
                                            class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Add Adjustment">
                                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none"><circle cx="8.5" cy="8.5" r="6.5" stroke="currentColor" stroke-width="1.5"/><path d="M8.5 5.5v6M5.5 8.5h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                    </button>
                                    {{-- Release --}}
                                    <form method="POST" action="{{ route('payroll.release', $pay) }}" onsubmit="return confirm('Release payroll for {{ $pay->employee->full_name }}?')">
                                        @csrf
                                        <button type="submit" class="text-navy hover:text-navy/70 transition-colors" title="Release">
                                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none"><path d="M3 8.5h10M9 4.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" class="text-gray-300"><rect x="6" y="4" width="28" height="32" rx="3" stroke="currentColor" stroke-width="2"/><path d="M12 14h16M12 20h16M12 26h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                <p class="text-[13px] text-gray-400">No payroll records yet.</p>
                                <a href="{{ route('payroll.run') }}" class="text-[13px] text-sky font-semibold hover:underline">Run your first payroll →</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payrolls->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $payrolls->links() }}</div>
    @endif


    {{-- ═══════════════════════════════════════════
         MODAL: View Payslip
    ═══════════════════════════════════════════ --}}
    <div x-show="showPayslip"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showPayslip = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-10 max-h-[90vh] overflow-y-auto">

            {{-- Payslip Header --}}
            <div class="bg-navy px-6 py-5 rounded-t-xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sky text-[10px] font-semibold uppercase tracking-widest">Payslip</p>
                        <p class="text-white text-[18px] font-bold mt-1">Blue Lotus Hotel</p>
                    </div>
                    <button @click="showPayslip = false" class="text-white/50 hover:text-white">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-sky/20 border border-sky/40 flex items-center justify-center text-sky text-[13px] font-bold"
                         x-text="selected ? (selected.employee?.first_name?.[0] ?? '') + (selected.employee?.last_name?.[0] ?? '') : ''"></div>
                    <div>
                        <p class="text-white font-semibold text-[14px]" x-text="selected ? selected.employee?.first_name + ' ' + selected.employee?.last_name : ''"></p>
                        <p class="text-white/50 text-[12px]" x-text="selected?.employee?.position ?? ''"></p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="text-white/50 text-[11px]">Pay Period</p>
                        <p class="text-white text-[12px] font-medium" x-text="selected ? formatPeriod(selected.period_start, selected.period_end) : ''"></p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-4">

                {{-- Hours & Basic Pay --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-[11px] text-gray-400 uppercase font-semibold">Hours Worked</p>
                        <p class="text-[22px] font-bold text-gray-900 mt-1" x-text="selected ? parseFloat(selected.total_hours).toFixed(1) + 'h' : '—'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-[11px] text-gray-400 uppercase font-semibold">Basic Pay</p>
                        <p class="text-[22px] font-bold text-gray-900 mt-1" x-text="selected ? '₱' + parseFloat(selected.basic_pay).toLocaleString('en-PH', {minimumFractionDigits:2}) : '—'"></p>
                    </div>
                </div>

                {{-- Adjustments breakdown --}}
                <div>
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-2">Breakdown</p>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">

                        {{-- Basic Pay row --}}
                        <div class="flex justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                            <span class="text-[13px] text-gray-700 font-medium">Basic Pay</span>
                            <span class="text-[13px] font-semibold text-gray-900" x-text="selected ? '₱' + parseFloat(selected.basic_pay).toFixed(2) : '—'"></span>
                        </div>

                        {{-- Bonuses --}}
                        <template x-if="selected?.adjustments?.filter(a => a.type === 'bonus').length > 0">
                            <div>
                                <div class="px-4 py-1.5 bg-green-50">
                                    <span class="text-[11px] font-semibold text-green-700 uppercase tracking-wide">Bonuses / Additions</span>
                                </div>
                                <template x-for="adj in selected?.adjustments?.filter(a => a.type === 'bonus')" :key="adj.id">
                                    <div class="flex justify-between px-4 py-2 border-b border-gray-50">
                                        <span class="text-[13px] text-gray-600" x-text="adj.description"></span>
                                        <span class="text-[13px] text-green-600 font-medium" x-text="'+ ₱' + parseFloat(adj.amount).toFixed(2)"></span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Gross total --}}
                        <div class="flex justify-between px-4 py-2.5 bg-blue-50/50 border-t border-blue-100">
                            <span class="text-[13px] font-bold text-gray-800">Gross Pay</span>
                            <span class="text-[13px] font-bold text-gray-900" x-text="selected ? '₱' + parseFloat(selected.gross_pay).toFixed(2) : '—'"></span>
                        </div>

                        {{-- Deductions --}}
                        <div class="px-4 py-1.5 bg-red-50">
                            <span class="text-[11px] font-semibold text-red-600 uppercase tracking-wide">Deductions</span>
                        </div>
                        <template x-for="adj in selected?.adjustments?.filter(a => a.type === 'deduction')" :key="adj.id">
                            <div class="flex justify-between px-4 py-2 border-b border-gray-50">
                                <span class="text-[13px] text-gray-600" x-text="adj.description"></span>
                                <span class="text-[13px] text-red-500" x-text="'- ₱' + parseFloat(adj.amount).toFixed(2)"></span>
                            </div>
                        </template>

                        {{-- Net Pay --}}
                        <div class="flex justify-between px-4 py-3 bg-navy">
                            <span class="text-white font-bold text-[14px]">Net Pay</span>
                            <span class="text-sky font-bold text-[18px]" x-text="selected ? '₱' + parseFloat(selected.net_pay).toLocaleString('en-PH', {minimumFractionDigits:2}) : '—'"></span>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="flex items-center justify-between pt-1">
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase font-semibold">Status</p>
                        <span class="inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                              :class="selected?.status === 'released' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'"
                              x-text="selected?.status === 'released' ? 'Released' : 'Draft'"></span>
                    </div>
                    <button @click="showPayslip = false"
                            class="h-9 px-5 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Close</button>
                </div>

            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════
         MODAL: Add Adjustment
    ═══════════════════════════════════════════ --}}
    <div x-show="showAdjust"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showAdjust = false"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-[16px] font-bold text-gray-900">Add Adjustment</h3>
                    <p class="text-[12px] text-gray-400 mt-0.5" x-text="selected ? selected.employee?.first_name + ' ' + selected.employee?.last_name : ''"></p>
                </div>
                <button @click="showAdjust = false" class="text-gray-400 hover:text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>
            <form method="POST" :action="selected ? '/payroll/' + selected.id + '/adjustments' : '#'" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Type <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="bonus" class="sr-only peer" required>
                            <div class="flex items-center justify-center gap-2 h-10 border border-gray-300 rounded-lg text-[13px] font-medium text-gray-600
                                        peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-colors">
                                <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 1v11M1 6.5h11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                Bonus
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="deduction" class="sr-only peer">
                            <div class="flex items-center justify-center gap-2 h-10 border border-gray-300 rounded-lg text-[13px] font-medium text-gray-600
                                        peer-checked:border-red-400 peer-checked:bg-red-50 peer-checked:text-red-600 transition-colors">
                                <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M1 6.5h11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                Deduction
                            </div>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Description <span class="text-red-500">*</span></label>
                    <input type="text" name="description" placeholder="e.g. 13th Month Pay, Absent Deduction"
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Amount (₱) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00"
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showAdjust = false" class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="h-9 px-5 bg-navy text-white text-[13px] font-semibold rounded-lg hover:bg-navy/90">Add</button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}

@push('scripts')
<script>
function payrollManager() {
    return {
        showPayslip: false,
        showAdjust:  false,
        selected:    null,

        openPayslip(p) { this.selected = p; this.showPayslip = true; },
        openAdjust(p)  { this.selected = p; this.showAdjust  = true; },

        formatPeriod(start, end) {
            if (!start || !end) return '';
            const s = new Date(start).toLocaleDateString('en-PH', { month: 'short', day: 'numeric' });
            const e = new Date(end).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
            return s + ' – ' + e;
        },
    }
}
</script>
@endpush

@endsection