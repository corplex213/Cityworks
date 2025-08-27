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
                                <button onclick="document.getElementById('registerModal').classList.remove('hidden')" 
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow whitespace-nowrap">
                                    + Register New User
                                </button>
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
                <div class="p-6">
                    @foreach($usersByPosition as $position => $users)
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <h4 class="text-md font-semibold text-gray-300">{{ $position }}</h4>
                                <span class="ml-2 px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded-full">
                                    {{ $users->count() }} {{ __('personnel') }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                                            <!-- Toggle Button -->
                                            <button @click="open = !open" class="focus:outline-none ml-2">
                                                <svg :class="{'rotate-180': open}" class="w-6 h-6 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Task Information -->
                                        <div class="mt-4" x-show="open" x-transition>
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
                                                <div class="space-y-3">
                                                    @foreach($userTasks as $task)
                                                        <div class="bg-gray-800 rounded-lg p-4">
                                                            <!-- Project Info -->
                                                            <div class="flex items-center justify-between mb-2">
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-project-diagram text-gray-400 mr-2"></i>
                                                                    <span class="text-sm font-medium text-gray-300">Activity:</span>
                                                                </div>
                                                                <span class="text-sm text-gray-400">
                                                                    @if($task->project)
                                                                        {{ $task->project->proj_name }}
                                                                    @else
                                                                        {{ __('Unassigned Project') }}
                                                                    @endif
                                                                </span>
                                                            </div>

                                                            <!-- Task Info -->
                                                            <div class="flex items-center justify-between mb-2">
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-clipboard-list text-gray-400 mr-2"></i>
                                                                    <span class="text-sm font-medium text-gray-300">Task:</span>
                                                                </div>
                                                                <span class="text-sm text-gray-400">{{ $task->task_name }}</span>
                                                            </div>

                                                            <!-- Status -->
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-info-circle text-gray-400 mr-2"></i>
                                                                    <span class="text-sm font-medium text-gray-300">Status:</span>
                                                                </div>
                                                                <span class="text-xs px-2 py-1 rounded-full 
                                                                    @if($task->status === 'Completed') bg-green-900 text-green-200
                                                                    @elseif($task->status === 'For Checking') bg-yellow-900 text-yellow-200
                                                                    @elseif($task->status === 'For Revision') bg-red-900 text-red-200
                                                                    @else bg-gray-900 text-gray-200
                                                                    @endif">
                                                                    {{ $task->status }}
                                                                </span>
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
            <!-- Modal for Register User -->
            <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-gray-800 p-8 rounded-lg w-full max-w-md relative">
                    <button
                        type="button"
                        onclick="document.getElementById('registerModal').classList.add('hidden')"
                        class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-full bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                        aria-label="Close modal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-bold text-gray-200 mb-4">Register New User</h2>
                    <!-- Session Status/Error -->
                    @if(session('status'))
                        <div class="mb-4 text-green-500 font-semibold">{{ session('status') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="mb-4 text-red-500 font-semibold">
                            {{ __('Please fix the errors below.') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('personnel.register') }}">
                        @csrf
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Enter full name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <!-- Email -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email') }}" required placeholder="Enter email address" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <!-- Position -->
                        <div class="mt-4">
                            <x-input-label for="position" :value="__('Position')" />
                            <select id="position" name="position" class="custom-select block mt-1 w-full bg-gray-800 text-white border-gray-700 rounded-md shadow-sm" required>
                                <option value="" disabled {{ old('position') ? '' : 'selected' }}>{{ __('Select a position') }}</option>
                                <optgroup label="Administrative Engineers">
                                    <option value="City Engineer" {{ old('position') == 'City Engineer' ? 'selected' : '' }}>{{ __('City Engineer') }}</option>
                                    <option value="Assistant City Engineer" {{ old('position') == 'Assistant City Engineer' ? 'selected' : '' }}>{{ __('Assistant City Engineer') }}</option>
                                    <option value="Supervising Administrative Officer" {{ old('position') == 'Supervising Administrative Officer' ? 'selected' : '' }}>{{ __('Supervising Administrative Officer') }}</option>
                                    <option value="Division Head" {{ old('position') == 'Division Head' ? 'selected' : '' }}>{{ __('Division Head') }}</option>
                                </optgroup>
                                <optgroup label="Managerial Engineers">
                                    <option value="Group Leaders" {{ old('position') == 'Group Leaders' ? 'selected' : '' }}>{{ __('Group Leaders') }}</option>
                                </optgroup>
                                <optgroup label="Staff Engineers">
                                    <option value="Technical Personnel" {{ old('position') == 'Technical Personnel' ? 'selected' : '' }}>{{ __('Technical Personnel') }}</option>
                                </optgroup>
                            </select>
                            <x-input-error :messages="$errors->get('position')" class="mt-2" />
                        </div>
                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow">
                                Register
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    <script>
        window.personnelRoute = "{{ route('personnel') }}";
    </script>
</x-app-layout> 