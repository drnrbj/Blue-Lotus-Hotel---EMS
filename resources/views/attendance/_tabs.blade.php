<div class="flex items-center gap-1 mb-5 bg-white border border-gray-200 rounded-lg p-1 w-fit">
    <a href="{{ route('attendance.index') }}"
       class="px-4 py-1.5 rounded-md text-[13px] font-medium transition-colors
              {{ $active === 'attendance' ? 'bg-navy text-white' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
        Attendance Records
    </a>
    <a href="{{ route('attendance.schedules') }}"
       class="px-4 py-1.5 rounded-md text-[13px] font-medium transition-colors
              {{ $active === 'schedules' ? 'bg-navy text-white' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
        Schedules
    </a>
    <a href="{{ route('attendance.leaves') }}"
       class="px-4 py-1.5 rounded-md text-[13px] font-medium transition-colors flex items-center gap-2
              {{ $active === 'leaves' ? 'bg-navy text-white' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
        Leave Requests
        @php $pending = \App\Models\Leave::where('status','pending')->count(); @endphp
        @if($pending > 0)
            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] font-bold
                         {{ $active === 'leaves' ? 'bg-white text-navy' : 'bg-red-500 text-white' }}">
                {{ $pending }}
            </span>
        @endif
    </a>
</div>