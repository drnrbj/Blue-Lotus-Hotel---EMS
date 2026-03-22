@extends('layouts.app')

@section('title', 'New Hires')
@section('subtitle', 'Pending employee profile creation')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')

{{-- ── Flash Messages ── --}}
@if(session('success'))
    <div class="mb-5 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg flex items-center gap-2">
        <svg width="16" height="16" fill="none" viewBox="0 0 16 16"><path d="M3 8l3 3 7-7" stroke="#16a34a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg border border-gray-200" x-data="{ showCreateModal: false, selected: null }">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">

            <div class="flex-shrink-0">
                <h2 class="text-[17px] font-bold text-gray-900">New Hires</h2>
                <p class="text-[13px] text-gray-400 mt-0.5">Employees waiting for profile creation</p>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('employees.new-hires') }}" class="flex flex-wrap items-center gap-2 lg:ml-auto">

                <select name="department_id"
                    class="h-9 px-3 pr-8 border border-gray-300 rounded-lg text-[13px] text-gray-700 bg-white focus:outline-none focus:border-sky appearance-none cursor-pointer"
                    style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%236b7280%22 d=%22M4 6l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:14px">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>

                <input type="date" name="from" value="{{ request('from') }}"
                       class="h-9 px-3 border border-gray-300 rounded-lg text-[13px] text-gray-700 focus:outline-none focus:border-sky">

                <input type="date" name="to" value="{{ request('to') }}"
                       class="h-9 px-3 border border-gray-300 rounded-lg text-[13px] text-gray-700 focus:outline-none focus:border-sky">

                <button type="submit"
                        class="h-9 px-4 bg-sky text-white text-[13px] font-semibold rounded-lg hover:bg-blue-500 transition-colors">
                    Apply
                </button>

                <a href="{{ route('employees.new-hires') }}"
                   class="h-9 px-4 flex items-center border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50 transition-colors">
                    Clear
                </a>
            </form>

        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Name</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Position</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Department</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Date Submitted</th>
                    <th class="text-left px-6 py-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($newHires as $hire)
                    @php
                        $words    = explode(' ', $hire->name);
                        $initials = strtoupper(substr($words[0] ?? '', 0, 1) . substr($words[1] ?? '', 0, 1));
                        $colors   = ['bg-blue-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-teal-500','bg-sky-500','bg-indigo-500'];
                        $color    = $colors[$hire->id % count($colors)];
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full {{ $color }} flex items-center justify-center text-white text-[12px] font-bold flex-shrink-0">
                                    {{ $initials }}
                                </div>
                                <span class="text-[13.5px] font-medium text-gray-900">{{ $hire->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-[13px] text-gray-700">{{ $hire->applied_position }}</td>
                        <td class="px-6 py-3.5">
                            @php
                                $deptColors = [
                                    'Food & Beverages' => 'bg-orange-100 text-orange-700',
                                    'Housekeeping'     => 'bg-blue-100 text-blue-700',
                                    'Human Resources'  => 'bg-purple-100 text-purple-700',
                                    'Front Office'     => 'bg-teal-100 text-teal-700',
                                    'Finance'          => 'bg-green-100 text-green-700',
                                    'IT'               => 'bg-gray-100 text-gray-600',
                                ];
                                $deptStyle = $deptColors[$hire->applied_position] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold {{ $deptStyle }}">
                                {{ $hire->applied_position }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700">
                                Pending
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-[13px] text-gray-500">
                            {{ \Carbon\Carbon::parse($hire->applied_date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-3.5">
                            <button type="button"
                                @click="selected = {{ $hire->toJson() }}; showCreateModal = true"
                                class="flex items-center gap-1.5 h-8 px-3 bg-emerald-500 hover:bg-emerald-600 text-white text-[12px] font-semibold rounded-lg transition-colors">
                                <svg width="13" height="13" viewBox="0 0 17 17" fill="none">
                                    <circle cx="6" cy="5.5" r="2.5" stroke="white" stroke-width="1.5"/>
                                    <path d="M1 14c0-2.8 2.2-4 5-4s5 1.2 5 4" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M12 7v4M14 9h-4" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                Create Profile
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-[13px] text-gray-400">
                            No new hires pending profile creation.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($newHires->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $newHires->links() }}
        </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════
         MODAL: CREATE EMPLOYEE PROFILE
    ═══════════════════════════════════════════════════════ --}}
    <div x-show="showCreateModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none;">

        <div class="absolute inset-0 bg-black/40" @click="showCreateModal = false"></div>

        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-[16px] font-bold text-gray-900">Create Employee Profile</h3>
                    <p class="text-[12px] text-gray-400 mt-0.5">Fill in the details to onboard this new hire</p>
                </div>
                <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 3l12 12M15 3L3 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>

            <form method="POST" :action="selected ? '/employees/new-hires/' + selected.id + '/create-profile' : '#'" class="p-6">
                @csrf

                {{-- Applicant Info Card --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 mb-5 text-[13px]">
                    <p class="text-gray-700"><span class="font-semibold">Applicant:</span> <span x-text="selected?.name"></span></p>
                    <p class="text-gray-700 mt-1"><span class="font-semibold">Applied Position:</span> <span x-text="selected?.applied_position"></span></p>
                </div>

                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Employee Code <span class="text-red-500">*</span></label>
                        <input type="text" name="employee_code" placeholder="EMP001"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Date Started <span class="text-red-500">*</span></label>
                        <input type="date" name="date_started"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Department <span class="text-red-500">*</span></label>
                        <select name="department_id" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                            <option value="">Select...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Position <span class="text-red-500">*</span></label>
                        <input type="text" name="position" :value="selected?.applied_position"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Employment Type <span class="text-red-500">*</span></label>
                        <select name="employment_type" class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                            <option value="">Select...</option>
                            <option value="full-time">Full-time</option>
                            <option value="part-time">Part-time</option>
                            <option value="contract">Contractual</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Date of Birth</label>
                        <input type="date" name="date_of_birth"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Phone</label>
                        <input type="text" name="phone_number"
                               class="w-full h-10 px-3 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Address</label>
                        <textarea name="address" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-[13px] focus:outline-none focus:border-sky resize-none"></textarea>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="showCreateModal = false"
                            class="h-9 px-4 border border-gray-300 rounded-lg text-[13px] text-gray-600 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="h-9 px-5 bg-emerald-500 hover:bg-emerald-600 text-white text-[13px] font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                        <svg width="13" height="13" viewBox="0 0 17 17" fill="none"><circle cx="6" cy="5.5" r="2.5" stroke="white" stroke-width="1.5"/><path d="M1 14c0-2.8 2.2-4 5-4s5 1.2 5 4" stroke="white" stroke-width="1.5" stroke-linecap="round"/><path d="M12 7v4M14 9h-4" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>
                        Create Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection