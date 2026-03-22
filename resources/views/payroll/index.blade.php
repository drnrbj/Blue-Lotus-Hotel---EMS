@extends('layouts.app')

@section('title', 'Payroll Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Payroll Management</h2>
            <p class="text-sm text-gray-600 mt-1">Manage employee salaries and payroll processing</p>
        </div>

        <div class="p-6">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Payroll System</h3>
                <p class="mt-1 text-sm text-gray-500">Payroll management functionality coming soon.</p>
                <div class="mt-6">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection