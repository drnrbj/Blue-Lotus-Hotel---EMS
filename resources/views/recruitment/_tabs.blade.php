<div class="flex items-center gap-1 mb-5 bg-white border border-gray-200 rounded-lg p-1 w-fit">
    <a href="{{ route('recruitment.index') }}"
       class="px-4 py-1.5 rounded-md text-[13px] font-medium transition-colors
              {{ $active === 'postings' ? 'bg-navy text-white' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
        Job Postings
    </a>
    <a href="{{ route('recruitment.applicants') }}"
       class="px-4 py-1.5 rounded-md text-[13px] font-medium transition-colors flex items-center gap-2
              {{ $active === 'applicants' ? 'bg-navy text-white' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
        Applicants
        @php $pending = \App\Models\Applicant::where('status','pending')->count(); @endphp
        @if($pending > 0)
            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] font-bold
                         {{ $active === 'applicants' ? 'bg-white text-navy' : 'bg-sky text-white' }}">
                {{ $pending }}
            </span>
        @endif
    </a>
    <a href="{{ route('recruitment.interviews') }}"
       class="px-4 py-1.5 rounded-md text-[13px] font-medium transition-colors flex items-center gap-2
              {{ $active === 'interviews' ? 'bg-navy text-white' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
        Interviews
        @php $scheduled = \App\Models\Interview::where('status','scheduled')->count(); @endphp
        @if($scheduled > 0)
            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] font-bold
                         {{ $active === 'interviews' ? 'bg-white text-navy' : 'bg-sky text-white' }}">
                {{ $scheduled }}
            </span>
        @endif
    </a>
</div>