<div>
    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Employee <span class="text-red-500">*</span></label>
    <select name="employee_id"
            class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
        <option value="">Select employee...</option>
        @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ isset($sched) && $sched?->employee_id == $emp->id ? 'selected' : '' }}>
                {{ $emp->full_name }} — {{ $emp->position }}
            </option>
        @endforeach
    </select>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Shift Start <span class="text-red-500">*</span></label>
        <input type="time" name="shift_start" value="{{ $sched?->shift_start }}"
               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
    </div>
    <div>
        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Shift End <span class="text-red-500">*</span></label>
        <input type="time" name="shift_end" value="{{ $sched?->shift_end }}"
               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
    </div>
</div>

<div>
    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-2">Work Days <span class="text-red-500">*</span></label>
    <div class="flex flex-wrap gap-2">
        @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
            @php $checked = isset($sched) && $sched && in_array($day, explode(',', $sched->days ?? '')); @endphp
            <label class="cursor-pointer">
                <input type="checkbox" name="days[]" value="{{ $day }}"
                       {{ $checked ? 'checked' : '' }} class="sr-only peer">
                <span class="inline-block w-10 text-center py-1.5 rounded border text-[12px] font-semibold
                             border-gray-300 text-gray-500
                             peer-checked:bg-navy peer-checked:text-white peer-checked:border-navy
                             hover:border-navy/50 transition-colors">
                    {{ $day }}
                </span>
            </label>
        @endforeach
    </div>
</div>

<div>
    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Effective Date <span class="text-red-500">*</span></label>
    <input type="date" name="effective_date"
           value="{{ $sched?->effective_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}"
           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
</div>