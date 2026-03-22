<div class="flex items-center gap-1 mb-5 bg-white border border-gray-200 rounded-lg p-1 w-fit">
    <a href="{{ route('performance.index') }}"
       class="px-4 py-1.5 rounded-md text-[13px] font-medium transition-colors
              {{ $active === 'list' ? 'bg-navy text-white' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
        Evaluations
    </a>
    <a href="{{ route('performance.analytics') }}"
       class="px-4 py-1.5 rounded-md text-[13px] font-medium transition-colors
              {{ $active === 'analytics' ? 'bg-navy text-white' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
        Analytics
    </a>
</div>