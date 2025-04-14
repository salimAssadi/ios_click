@extends('tenant::layouts.app')

@section('title', 'Dashboard - ISO Tracker')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium mb-4">Welcome back, {{ Auth::user()->name }}!</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Documents Overview -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h4 class="font-semibold text-lg mb-4">Documents</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Documents</span>
                                <span class="font-semibold">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Pending Review</span>
                                <span class="font-semibold">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Recently Updated</span>
                                <span class="font-semibold">0</span>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="" class="text-indigo-600 hover:text-indigo-900">View all documents →</a>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h4 class="font-semibold text-lg mb-4">Quick Actions</h4>
                        <div class="space-y-4">
                            <a href="" class="block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-center">
                                Create New Document
                            </a>
                            <a href="#" class="block px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 text-center">
                                Import from Google Docs
                            </a>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h4 class="font-semibold text-lg mb-4">Recent Activity</h4>
                        <div class="space-y-4">
                            <p class="text-gray-600 text-center py-8">No recent activity</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
