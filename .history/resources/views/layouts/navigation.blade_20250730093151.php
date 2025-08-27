<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('frontend/img/baguio-logo.png') }}" alt="Baguio Logo" class="block h-9 w-auto">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex sm:mr-3">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('calendar')" :active="request()->routeIs('calendar')">
                        {{ __('Calendar') }}
                    </x-nav-link>

                    <x-nav-link :href="route('projects')" :active="request()->routeIs('projects')">
                        {{ __('Activities') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('personnel')" :active="request()->routeIs('personnel')">
                        {{ __('Personnel') }}
                    </x-nav-link>

                    <x-nav-link :href="route('userAccessControl')" :active="request()->routeIs('userAccessControl')">
                        {{ __('User Access Control') }}
                    </x-nav-link>

                    <x-nav-link :href="route('archiveProjects')" :active="request()->routeIs('archiveProjects')">
                        {{ __('Archive') }}
                    </x-nav-link>
                </div>   
            </div>

            <!-- Right-side Actions: Notification + Settings -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4 sm:ms-6">
                <!-- Notification Button and Dropdown -->
                <div class="relative" x-data="notificationSystem">
                    <!-- Button -->
                    <button @click="showNotifications = !showNotifications"
                        class="relative inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                        <svg class="h-6 w-6 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span x-show="unreadCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" x-text="unreadCount"></span>
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="showNotifications" x-transition
                        class="absolute right-0 mt-2 w-[600px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50"
                        @click.away="showNotifications = false"
                        style="display: none;">
                        
                        <!-- Tabs -->
                        <div class="flex border-b border-gray-200 dark:border-gray-700">
                            <button @click="switchTab('all')" 
                                class="flex-1 py-3 px-4 text-center text-sm font-medium"
                                :class="activeTab === 'all' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                                All
                            </button>
                            <button @click="switchTab('mentions')" 
                                class="flex-1 py-3 px-4 text-center text-sm font-medium"
                                :class="activeTab === 'mentions' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                                Mentions & Comments
                            </button>
                            <button @click="switchTab('tasks')" 
                                class="flex-1 py-3 px-4 text-center text-sm font-medium"
                                :class="activeTab === 'tasks' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">
                                Assigned Tasks
                            </button>
                        </div>
                        
                        <!-- Notifications Content -->
                        <div class="max-h-[600px] overflow-y-auto">
                            <div x-show="activeTab === 'all'" class="p-4">
                                <div id="all-notifications" class="space-y-4">
                                    <!-- All notifications will be loaded here -->
                                    <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                        Loading notifications...
                                    </div>
                                </div>
                            </div>
                            
                            <div x-show="activeTab === 'mentions'" class="p-4">
                                <div id="mentions-notifications" class="space-y-4">
                                    <!-- Mentions and comments will be loaded here -->
                                    <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                        Loading mentions and comments...
                                    </div>
                                </div>
                            </div>
                            
                            <div x-show="activeTab === 'tasks'" class="p-4">
                                <div id="tasks-notifications" class="space-y-4">
                                    <!-- Task notifications will be loaded here -->
                                    <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                        Loading task notifications...
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer with Mark All as Read and Delete All buttons -->
                        <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                            <button @click="markAllAsRead()" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                Mark all as read
                            </button>
                            <button @click="deleteAllNotifications()" class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                Delete all notifications
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="flex items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Notification JavaScript -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notificationSystem', () => ({
            showNotifications: false,
            activeTab: 'all',
            unreadCount: 0,
            notifications: {
                all: [],
                mentions: [],
                tasks: []
            },
            
            init() {
                this.fetchUnreadCount();
                this.fetchAllNotifications();
            },
            
            fetchUnreadCount() {
                fetch('{{ route("notifications.unreadCount") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.count !== undefined) {
                            this.unreadCount = data.count;
                        }
                    })
                    .catch(error => console.error('Error fetching unread count:', error));
            },
            
            fetchAllNotifications() {
                // Fetch all notifications at once
                fetch('{{ route("notifications.index") }}?type=all')
                    .then(response => response.json())
                    .then(data => {
                        if (data.notifications) {
                            this.notifications.all = data.notifications;
                            this.notifications.mentions = data.notifications.filter(n => 
                                n.type === 'comment_added' || n.type === 'comment_reply' || n.type === 'mention'
                            );
                            this.notifications.tasks = data.notifications.filter(n => 
                                n.type === 'task_assigned' || n.type === 'task_completed'
                            );
                            this.updateActiveTab();
                        }
                    })
                    .catch(error => console.error('Error fetching notifications:', error));
            },
            
            updateActiveTab() {
                const container = document.getElementById(`${this.activeTab}-notifications`);
                if (!container) return;
                
                const notifications = this.notifications[this.activeTab];
                
                if (!notifications || notifications.length === 0) {
                    container.innerHTML = `<div class="text-center text-gray-500 dark:text-gray-400 py-4">
                        ${this.activeTab === 'all' ? 'No notifications' : 
                        this.activeTab === 'mentions' ? 'No mentions or comments yet' : 
                        'No assigned tasks yet'}
                    </div>`;
                    return;
                }

                let html = '';
                notifications.forEach(notification => {
                    const isUnread = !notification.read;
                    const unreadClass = isUnread ? 'bg-blue-50 dark:bg-blue-900/20' : '';
                    
                    // Format display title and message consistently
                    let displayTitle = notification.title;
                    let displayMessage = notification.message;
                    
                    if (notification.type === 'mention') {
                        displayTitle = 'You were mentioned';
                        const mentioner = notification.message.split(' ')[0];
                        displayMessage = `${mentioner} mentioned you in a comment`;
                    } else if (notification.type === 'comment_added') {
                        displayTitle = 'New Comment on Task';
                    } else if (notification.type === 'comment_reply') {
                        displayTitle = 'New Reply to Comment';
                    }
                    
                    html += `
                        <div class="p-4 rounded-lg ${unreadClass} hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    ${this.getNotificationIcon(notification.type)}
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        ${displayTitle}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 break-all">
                                        ${displayMessage}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        ${this.formatDate(notification.created_at)}
                                    </p>
                                    <div class="mt-2 flex space-x-3">
                                        <a href="${notification.link}" class="text-sm text-blue-500">View</a>
                                        ${isUnread ? `<button x-on:click.prevent="markAsRead(${notification.id})" class="text-sm text-gray-500">Mark as read</button>` : ''}
                                        <button x-on:click.prevent="deleteNotification(${notification.id})" class="text-sm text-red-500">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = html;

                // Add event listeners for buttons
                if (notifications.length > 0) {
                    // Using setTimeout to ensure the DOM is updated before we attach events
                    setTimeout(() => {
                        // We need to handle events here since we're using string templates
                        document.querySelectorAll(`#${this.activeTab}-notifications .text-sm.text-gray-500`).forEach(button => {
                            button.addEventListener('click', (e) => {
                                const id = e.target.getAttribute('data-id');
                                if (id) this.markAsRead(parseInt(id));
                            });
                        });
                        
                        document.querySelectorAll(`#${this.activeTab}-notifications .text-sm.text-red-500`).forEach(button => {
                            button.addEventListener('click', (e) => {
                                const id = e.target.getAttribute('data-id');
                                if (id) this.deleteNotification(parseInt(id));
                            });
                        });
                    }, 0);
                }
            },

            // Update the tab click handlers
            switchTab(tab) {
                this.activeTab = tab;
                this.updateActiveTab();
            },

            // Update the markAsRead and deleteNotification methods to refresh all notifications
            markAsRead(id) {
                fetch(`{{ url('notifications') }}/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update local notification data
                        this.notifications.all.forEach(notification => {
                            if (notification.id === id) {
                                notification.read = true;
                            }
                        });
                        this.notifications.mentions.forEach(notification => {
                            if (notification.id === id) {
                                notification.read = true;
                            }
                        });
                        this.notifications.tasks.forEach(notification => {
                            if (notification.id === id) {
                                notification.read = true;
                            }
                        });
                        
                        // Update the UI
                        this.fetchUnreadCount();
                        this.updateActiveTab();
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
            },
            
            markAllAsRead() {
                fetch('{{ route("notifications.markAllAsRead") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.unreadCount = 0; // Reset unread count
                        
                        // Mark all notifications as read in all categories
                        Object.keys(this.notifications).forEach(key => {
                            this.notifications[key].forEach(notification => {
                                notification.read = true;
                            });
                        });
                        
                        // Refresh the UI
                        this.updateActiveTab(); 
                    }
                })
                .catch(error => console.error('Error marking all notifications as read:', error));
            },

            deleteNotification(id) {
                if (confirm('Are you sure you want to delete this notification?')) {
                    fetch(`{{ url('notifications') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove notification from all arrays
                            this.notifications.all = this.notifications.all.filter(n => n.id !== id);
                            this.notifications.mentions = this.notifications.mentions.filter(n => n.id !== id);
                            this.notifications.tasks = this.notifications.tasks.filter(n => n.id !== id);
                            
                            // Update the UI
                            this.fetchUnreadCount();
                            this.updateActiveTab();
                        }
                    })
                    .catch(error => console.error('Error deleting notification:', error));
                }
            },

            deleteAllNotifications() {
                if (confirm('Are you sure you want to delete all notifications? This action cannot be undone.')) {
                    fetch('{{ route("notifications.deleteAll") }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.unreadCount = 0;
                            this.notifications = {
                                all: [],
                                mentions: [],
                                tasks: []
                            };
                            this.updateActiveTab();
                        }
                    })
                    .catch(error => console.error('Error deleting all notifications:', error));
                }
            },
            
            getNotificationIcon(type) {
                switch (type) {
                    case 'task_assigned':
                        return `
                            <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        `;
                    case 'task_completed':
                        return `
                            <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        `;
                    case 'mention':
                        return `
                            <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                        `;
                    case 'comment_added':
                    case 'comment_reply':
                        return `
                            <div class="h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                        `;
                    default:
                        return `
                            <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <svg class="h-6 w-6 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        `;
                }
            },
            
            formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);
                
                if (diffInSeconds < 60) {
                    return 'Just now';
                } else if (diffInSeconds < 3600) {
                    const minutes = Math.floor(diffInSeconds / 60);
                    return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
                } else if (diffInSeconds < 86400) {
                    const hours = Math.floor(diffInSeconds / 3600);
                    return `${hours} hour${hours > 1 ? 's' : ''} ago`;
                } else {
                    return date.toLocaleDateString();
                }
            }
        }));
    });
</script>

