<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
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
                            <p class="text-gray-400">Active Projects</p>
                            <h3 class="text-2xl font-bold text-gray-200">{{ $tasks->pluck('project')->unique()->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personnel List -->
            <div class="bg-gray-800 rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-700">
                    <div class="flex items-center gap-2 w-full max-w-xl justify-end">
                        <button onclick="document.getElementById('registerModal').classList.remove('hidden')" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow">
                            + Register New User
                        </button>
                        <div class="relative w-full">
                            <form method="GET" action="{{ route('personnel') }}" class="flex items-center">
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
                            </form>
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
                                                                    <span class="text-sm font-medium text-gray-300">Project:</span>
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
                    <button onclick="document.getElementById('registerModal').classList.add('hidden')" 
                            class="absolute top-2 right-2 text-gray-400 hover:text-gray-200">&times;</button>
                    <h2 class="text-xl font-bold text-gray-200 mb-4">Register New User</h2>
                    <form method="POST" action="{{ route('personnel.register') }}">
                        @csrf
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <!-- Email -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <!-- Position -->
                        <div class="mt-4">
                            <x-input-label for="position" :value="__('Position')" />
                            <select id="position" name="position" class="custom-select block mt-1 w-full bg-gray-800 text-white border-gray-700 rounded-md shadow-sm" required>
                                <option value="" disabled selected>{{ __('Select a position') }}</option>
                                <optgroup label="Administrative Positions">
                                    <option value="City Engineer">{{ __('City Engineer') }}</option>
                                    <option value="Assistant City Engineer">{{ __('Assistant City Engineer') }}</option>
                                    <option value="Supervising Administrative Officer">{{ __('Supervising Administrative Officer') }}</option>
                                    <option value="Division Head">{{ __('Division Head') }}</option>
                                </optgroup>
                                <optgroup label="Managerial Position">
                                    <option value="Group Leaders">{{ __('Group Leaders') }}</option>
                                </optgroup>
                                <optgroup label="Operational Position">
                                    <option value="Technical Personnel">{{ __('Technical Personnel') }}</option>
                                </optgroup>
                            </select>
                            <x-input-error :messages="$errors->get('position')" class="mt-2" />
                        </div>
                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
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
    @push('styles')
    <style>
        .org-child::before {
            content: '';
            position: absolute;
            top: -1rem;
            left: 50%;
            width: 2px;
            height: 1rem;
            background: #4B5563;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function clearSearchBar() {
            const searchInput = document.getElementById('searchInput');
            searchInput.value = '';
            window.location.href = '{{ route("personnel") }}';
        }

        // Add debounce function to prevent too many searches
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Handle search input with URL updates
        const handleSearch = debounce((searchValue) => {
            const url = new URL(window.location.href);
            if (searchValue) {
                url.searchParams.set('search', searchValue);
            } else {
                url.searchParams.delete('search');
            }
            window.history.pushState({}, '', url);
            
            // Submit the form
            document.querySelector('form').submit();
        }, 500);

        // Add event listener to search input
        document.getElementById('searchInput').addEventListener('input', (e) => {
            handleSearch(e.target.value);
        });

        // Initialize search if there's a value in the URL
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const searchValue = urlParams.get('search');
            if (searchValue) {
                document.getElementById('searchInput').value = searchValue;
            }
        });
        function filterPersonnel() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const personnelCards = document.querySelectorAll('.bg-gray-700.rounded-lg'); // Select all personnel cards
            let visibleCount = 0;

            personnelCards.forEach((card) => {
                const name = card.querySelector('.font-semibold.text-gray-200').textContent.toLowerCase();
                const email = card.querySelector('.text-sm.text-gray-400').textContent.toLowerCase();
                const position = card.closest('.mb-8').querySelector('.text-md.font-semibold.text-gray-300').textContent.toLowerCase();
                const tasks = Array.from(card.querySelectorAll('.bg-gray-800 .text-gray-400')).map(el => el.textContent.toLowerCase());

                if (
                    name.includes(searchInput) ||
                    email.includes(searchInput) ||
                    position.includes(searchInput) ||
                    tasks.some(task => task.includes(searchInput))
                ) {
                    card.style.display = ''; // Show the card
                    card.closest('.mb-8').style.display = ''; // Show the position section
                    visibleCount++;
                } else {
                    card.style.display = 'none'; // Hide the card
                    
                    // Check if all cards in this position are hidden
                    const positionSection = card.closest('.mb-8');
                    const visibleCardsInPosition = positionSection.querySelectorAll('.bg-gray-700.rounded-lg[style="display: "]');
                    if (visibleCardsInPosition.length === 0) {
                        positionSection.style.display = 'none'; // Hide the position section
                    }
                }
            });

            // Show "No results found" message if no cards are visible
            const noResultsMessage = document.getElementById('noResultsMessage');
            if (!noResultsMessage) {
                const container = document.querySelector('.p-6');
                const messageDiv = document.createElement('div');
                messageDiv.id = 'noResultsMessage';
                messageDiv.className = 'text-center text-gray-400 py-8 hidden';
                messageDiv.innerHTML = '<i class="fas fa-search mb-2 text-2xl"></i><p>No personnel found matching your search.</p>';
                container.appendChild(messageDiv);
            }
            
            noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';

            // Update counts in position headers
            document.querySelectorAll('.mb-8').forEach(section => {
                const visibleCards = section.querySelectorAll('.bg-gray-700.rounded-lg[style="display: "]');
                const countBadge = section.querySelector('.text-xs.bg-gray-700');
                if (countBadge) {
                    countBadge.textContent = `${visibleCards.length} personnel`;
                }
            });

            // Update total count in stats
            const totalCount = document.querySelector('.text-2xl.font-bold.text-gray-200');
            if (totalCount) {
                totalCount.textContent = visibleCount.toString();
            }
        }
        document.getElementById('searchInput').addEventListener('input', filterPersonnel);
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            if (searchInput.value) {
                filterPersonnel();
            }
        });
    </script>
    @endpush
</x-app-layout> 