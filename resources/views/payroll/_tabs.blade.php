<div class="flex items-center gap-1 mb-5 bg-white border border-gray-200 rounded-lg p-1 w-fit">
    <a href="{{ route('payroll.index') }}"
       class="px-4 py-1.5 rounded-md text-[13px] font-medium transition-colors
              {{ $active === 'list' ? 'bg-navy text-white' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
        Payroll List
    </a>
    <a href="{{ route('payroll.run') }}"
       class="px-4 py-1.5 rounded-md text-[13px] font-medium transition-colors
              {{ $active === 'run' ? 'bg-navy text-white' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
        Run Payroll
    </a>
    <a href="{{ route('payroll.reports') }}"
       class="px-4 py-1.5 rounded-md text-[13px] font-medium transition-colors
              {{ $active === 'reports' ? 'bg-navy text-white' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
        Reports
    </a>
</div>