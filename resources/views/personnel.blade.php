<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            @vite(['resources/js/personnel.js'])
            {{ __('Personnel Management') }}
        </h2>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    </x-slot>

            <div class="py-12 bg-gray-900">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <!-- Stats Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-gray-600">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-gray-700 text-gray-300">
                                    <i class="fas fa-users text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-gray-400">Total Personnel</p>
                                    <h3 class="text-2xl font-bold text-gray-200">{{ $usersByPosition->flatten()->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-gray-600">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-gray-700 text-gray-300">
                                    <i class="fas fa-tasks text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-gray-400">Active Tasks</p>
                                    <h3 class="text-2xl font-bold text-gray-200">{{ $tasks->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-gray-600">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-gray-700 text-gray-300">
                                    <i class="fas fa-project-diagram text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-gray-400">Active Activities</p>
                                    <h3 class="text-2xl font-bold text-gray-200">{{ $tasks->pluck('project')->unique()->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Personnel List -->
                    <div class="bg-gray-800 rounded-xl shadow-lg">
                        <div class="p-6 border-b border-gray-700">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-200 flex items-center">
                                    <i class="fas fa-users mr-2 text-gray-400"></i>
                                    {{ __('Personnel List') }}
                                </h3>
                                <div class="flex items-center gap-2 w-full md:w-auto">
                                    <div class="flex items-center gap-2 w-full">
                                        <div class="relative w-full max-w-lg">
                                            <div class="flex items-center">
                                                <input id="searchInput" 
                                                    name="search" 
                                                    type="text" 
                                                    placeholder="Search personnel..." 
                                                    value="{{ request('search') }}"
                                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 pl-10 pr-10 rounded-lg shadow focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                                <!-- Search Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                                <!-- Clear Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 cursor-pointer hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" onclick="clearSearchBar()">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6" id="personnelListContainer">
                            @foreach($usersByPosition as $position => $users)
                                <div class="mb-8">
                                    <div class="flex items-center mb-4">
                                        <h4 class="text-md font-semibold text-gray-300">{{ $position }}</h4>
                                        <span class="ml-2 px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded-full">
                                            {{ $users->count() }} {{ __('personnel') }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
                                        @foreach($users as $user)
                                            <div class="bg-gray-700 rounded-lg p-6 border border-gray-600 hover:border-gray-500 transition duration-200" x-data="{ open: false }">
                                                <!-- Personnel Info Header -->
                                                <div class="flex items-center border-b border-gray-600 pb-4 justify-between">
                                                    <div class="flex items-center">
                                                        <div class="w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center">
                                                            <span class="text-xl font-semibold text-gray-200">
                                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="font-semibold text-gray-200">{{ $user->name }}</div>
                                                            <div class="text-sm text-gray-400">{{ $user->email }}</div>
                                                        </div>
                                                    </div>
                                                    <!-- Toggle Button with Tooltip -->
                                                    <button @click="open = !open" class="focus:outline-none ml-2" title="Show/hide assigned tasks">
                                                        <svg :class="{ 'rotate-180': open }" class="w-6 h-6 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                
                                                <!-- Task Information -->
                                                <div class="mt-4" x-show="open" x-transition x-cloak>
                                                    <div class="flex items-center justify-between mb-3">
                                                        <h5 class="text-sm font-semibold text-gray-300 flex items-center">
                                                            <i class="fas fa-tasks mr-2 text-gray-400"></i>
                                                            {{ __('Assigned Tasks') }}
                                                        </h5>
                                                        @php
                                                            $userTasks = $tasks->filter(function($task) use ($user) {
                                                                return $task->assignedUser && $task->assignedUser->id === $user->id;
                                                            });
                                                        @endphp
                                                        <span class="text-xs px-2 py-1 bg-gray-800 text-gray-400 rounded-full">
                                                            {{ $userTasks->count() }} {{ Str::plural('task', $userTasks->count()) }}
                                                        </span>
                                                    </div>
                                                    
                                                    @if($userTasks->count() > 0)
                                                        <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                                                            @foreach($userTasks as $task)
                                                                <div class="bg-gray-800 rounded-lg p-4 space-y-2">
                                                                    <!-- Activity -->
                                                                    <div class="flex items-start gap-2">
                                                                        <i class="fas fa-project-diagram text-gray-400 mt-1"></i>
                                                                        <div>
                                                                            <span class="text-xs font-semibold text-gray-400">Activity:</span>
                                                                            <span class="text-sm font-semibold text-gray-200">{{ $task->project ? $task->project->proj_name : __('Unassigned Project') }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Task -->
                                                                    <div class="flex items-start gap-2">
                                                                        <i class="fas fa-clipboard-list text-gray-400 mt-1"></i>
                                                                        <div>
                                                                            <span class="text-xs font-semibold text-gray-400">Task:</span>
                                                                            <span class="text-sm text-gray-200">{{ $task->task_name }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Status -->
                                                                    <div class="flex items-start gap-2">
                                                                        <i class="fas fa-info-circle text-gray-400 mt-1"></i>
                                                                        <div>
                                                                            <span class="text-xs font-semibold text-gray-400">Status:</span>
                                                                            <span class="text-xs px-2 py-1 rounded-full
                                                                                @if($task->status === 'Completed') bg-green-700 text-white
                                                                                @elseif($task->status === 'For Checking') bg-yellow-700 text-white
                                                                                @elseif($task->status === 'For Revision') bg-red-700 text-white
                                                                                @elseif($task->status === 'Deferred') bg-gray-600 text-white
                                                                                @else bg-gray-900 text-gray-200
                                                                                @endif">
                                                                                {{ $task->status }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="bg-gray-800 rounded-lg p-4 text-center">
                                                            <i class="fas fa-inbox text-gray-400 mb-2 text-lg"></i>
                                                            <p class="text-sm text-gray-400">{{ __('No tasks assigned') }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.personnelRoute = "{{ route('personnel') }}";
        });
    </script>
</x-app-layout> 
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>