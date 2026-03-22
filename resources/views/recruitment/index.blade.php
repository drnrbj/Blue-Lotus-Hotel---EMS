@extends('layouts.app')

@section('title', 'Recruitment Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Recruitment Management</h2>
            <p class="text-sm text-gray-600 mt-1">Manage job applications and hiring process</p>
        </div>

        <div class="p-6">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0V8a2 2 0 01-2 2H8a2 2 0 01-2-2V6m8 0H8m0 0V4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Recruitment System</h3>
                <p class="mt-1 text-sm text-gray-500">Recruitment management functionality coming soon.</p>
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