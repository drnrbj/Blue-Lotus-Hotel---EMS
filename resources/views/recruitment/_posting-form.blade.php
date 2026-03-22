<div>
    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Job Title <span class="text-red-500">*</span></label>
    <input type="text" name="job_title" value="{{ $post?->job_title }}" placeholder="e.g. Front Desk Officer"
           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Department</label>
        <select name="department_id" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
            <option value="">Select...</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ $post?->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Slots <span class="text-red-500">*</span></label>
        <input type="number" name="slots" value="{{ $post?->slots ?? 1 }}" min="1"
               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Posting Date <span class="text-red-500">*</span></label>
        <input type="date" name="posting_date" value="{{ $post?->posting_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}"
               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
    </div>
    <div>
        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Deadline</label>
        <input type="date" name="deadline" value="{{ $post?->deadline?->format('Y-m-d') }}"
               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">
    </div>
</div>
<div>
    <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Description</label>
    <textarea name="description" rows="3" placeholder="Role overview, requirements..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky resize-none">{{ $post?->description }}</textarea>
</div>