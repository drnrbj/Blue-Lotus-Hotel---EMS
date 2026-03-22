@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Attendance Management</h2>
            <p class="text-sm text-gray-600 mt-1">Track employee attendance and time records</p>
        </div>

        <div class="p-6">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Attendance Tracking</h3>
                <p class="mt-1 text-sm text-gray-500">Attendance management functionality coming soon.</p>
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