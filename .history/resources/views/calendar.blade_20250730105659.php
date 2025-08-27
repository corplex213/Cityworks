<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Calendar of Tasks') }}
    </h2>
    @vite(['resources/js/fullCalendar.js', 'resources/css/fullCalendar.css'])
    <!-- Add explicit height for calendar -->
</x-slot>

    <div class="flex h-[calc(100vh-5rem)] overflow-hidden">
    <main class="flex-1 flex flex-col bg-slate-800">
        <div class="bg-slate-700 p-3 flex flex-wrap items-center justify-between gap-2">
            <!-- Task Status Filter -->
            <div class="flex items-center gap-2">
                <label class="text-white text-sm">My Tasks:</label>
                <div class="flex gap-1">
                    <button class="task-filter active px-2 py-1 text-xs rounded bg-blue-600 text-white" data-filter="all">All</button>
                    <button class="task-filter px-2 py-1 text-xs rounded bg-slate-600 hover:bg-blue-600 text-white" data-filter="ongoing">Ongoing</button>
                    <button class="task-filter px-2 py-1 text-xs rounded bg-slate-600 hover:bg-green-600 text-white" data-filter="completed">Completed</button>
                    <button class="task-filter px-2 py-1 text-xs rounded bg-slate-600 hover:bg-red-600 text-white" data-filter="deferred">Deferred</button>
                </div>
            </div>
            <!-- Right controls: User Filter + Export Button -->
            <div class="flex items-center gap-2">
                <label class="text-white text-sm">Show tasks from:</label>
                <select id="user-filter" class="text-sm rounded bg-slate-800 text-white border-slate-700 px-2 py-1">
                    <option value="mine">Only mine</option>
                    <option value="all">All users</option>
                    <!-- Additional user options will be populated by JS -->
                </select>
                <button id="export-csv-btn" class="px-3 py-2 bg-slate-500 hover:bg-slate-600 text-white rounded text-xs transition">
                    Export Calendar as CSV
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-4">
            <div id="calendar-root"></div>
        </div>
    </main>
        <!-- Right: Task List (moved to the right) -->
        <aside class="w-full max-w-xs bg-slate-900 border-l border-slate-800 p-4 flex flex-col">
            <button id="open-create-task-modal" aria-label="Create new event" class="mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                + Create Event
            </button>
            <div class="mb-6">
                <button id="toggle-my-tasks" class="flex items-center w-full text-left text-white font-semibold mb-2 focus:outline-none">
                    <svg id="mytasks-chevron" class="w-4 h-4 mr-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    My Tasks ( <span id="ongoing-count">0</span> )
                </button>
                <ul id="user-task-list" class="space-y-2 overflow-y-auto max-h-[40vh]">
                    <!-- JS will populate this list -->
                </ul>
            </div>
            <div class="mb-6">
                <button id="toggle-my-events" class="flex items-center w-full text-left text-indigo-400 font-semibold mb-2 focus:outline-none">
                    <svg id="myevents-chevron" class="w-4 h-4 mr-2 transition-transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    My Events ( <span id="event-count">0</span> )
                </button>
                <ul id="user-event-list" class="space-y-2 overflow-y-auto max-h-[20vh] hidden">
                    <!-- JS will populate this list -->
                </ul>
            </div>
            <div class="mb-6">
                <button id="toggle-completed-tasks" class="flex items-center w-full text-left text-green-400 font-semibold mb-2 focus:outline-none">
                    <svg id="completed-chevron" class="w-4 h-4 mr-2 transition-transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Completed ( <span id="completed-count">0</span> )
                </button>
                <ul id="completed-task-list" class="space-y-2 overflow-y-auto max-h-[20vh] hidden">
                    <!-- JS will populate this list -->
                </ul>
            </div>
            <div class="mb-6">
                <button id="toggle-deferred-tasks" class="flex items-center w-full text-left text-red-400 font-semibold mb-2 focus:outline-none">
                    <svg id="deferred-chevron" class="w-4 h-4 mr-2 transition-transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Deferred ( <span id="deferred-count">0</span> )
                </button>
                <ul id="deferred-task-list" class="space-y-2 overflow-y-auto max-h-[20vh] hidden">
                    <!-- JS will populate this list -->
                </ul>
            </div>
        </aside>
    </div>

    <!-- Add Event Modal -->
    <div id="create-task-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm hidden">
    <div id="createTaskContent"
        class="opacity-0 scale-95 transform transition-all duration-300 ease-out bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-full max-w-md">
        <button id="close-create-task-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 text-2xl">&times;</button>
        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Add Event</h2>
        <form id="create-task-form" class="space-y-4">
            <div id="create-task-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-2 text-sm"></div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Event Name</label>
                <input type="text" name="task_name"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Date</label>
                <input type="date" name="date"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Start Time</label>
                <input type="time" name="start_time"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">End Time</label>
                <input type="time" name="end_time"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="description"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" id="close-create-task-modal-btn"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg mr-2 transition">Cancel</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Save</button>
            </div>
        </form>
    </div>
</div>

    <!-- Event Modal (Google Calendar Style) -->
    <div id="event-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative overflow-hidden">
            <!-- Banner/Image (optional, can use a placeholder or project image) -->
            <div class="bg-blue-100 h-32 flex items-center justify-center">
                <svg class="w-20 h-20 text-blue-500" fill="none" viewBox="0 0 48 48">
                    <rect x="10" y="12" width="28" height="32" rx="4" fill="#3b82f6"/>
                    <rect x="16" y="8" width="16" height="8" rx="2" fill="#fff"/>
                    <rect x="18" y="20" width="12" height="2" rx="1" fill="#fff"/>
                    <rect x="18" y="26" width="12" height="2" rx="1" fill="#fff"/>
                    <rect x="18" y="32" width="8" height="2" rx="1" fill="#fff"/>
                    <circle cx="24" cy="12" r="2" fill="#60a5fa"/>
                </svg>
            </div>
            <div class="absolute top-3 right-3 flex items-center gap-4 z-10">
                <!-- Delete (Trash) Icon -->
                <button id="delete-event-btn" title="Delete Event" aria-label="Delete Event"
                    class="text-red-600 hover:text-red-700 transition p-1 rounded-full hover:bg-red-100 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v2H9V4a1 1 0 011-1zm-7 4h18" />
                    </svg>
                </button>
                <!-- Close (X) Icon as SVG -->
                <button id="close-modal" title="Close" aria-label="Close"
                    class="text-gray-400 hover:text-gray-700 transition p-1 rounded-full hover:bg-gray-200 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <!-- Content -->
            <div class="p-6">
                <div class="flex items-center mb-2">
                    <span id="modal-status-dot" class="w-3 h-3 rounded-full mr-2"></span>
                    <h2 id="modal-title" class="text-lg font-bold text-gray-800"></h2>
                </div>
                <div class="text-gray-500 mb-4" id="modal-date"></div>
                <div class="space-y-2 text-gray-700">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                        </svg>
                        <span id="modal-project"></span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                        </svg>
                        <span id="modal-priority"></span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span id="modal-assigned"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('export-csv-btn').addEventListener('click', function() {
        // Get events from FullCalendar or your event source
        let events = window.calendar ? window.calendar.getEvents() : [];

        if (!events.length) {
            alert('No events to export.');
            return;
        }

        // CSV header
        let csvRows = [
            ['Task/Events', 'Start Date', 'Start Time', 'End Date', 'End Time', 'Description']
        ];

        events.forEach(event => {
            // Adjust these fields as per your event object structure
            let start = event.start ? new Date(event.start) : null;
            let end = event.end ? new Date(event.end) : start;

            let startDate = start ? start.toLocaleDateString('en-CA') : '';
            let startTime = start ? start.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }) : '';
            let endDate = end ? end.toLocaleDateString('en-CA') : '';
            let endTime = end ? end.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }) : '';

            let summary = event.title || 'Event';
            let description = event.extendedProps && event.extendedProps.description ? event.extendedProps.description : '';

            csvRows.push([
                `"${summary.replace(/"/g, '""')}"`,
                startDate,
                startTime,
                endDate,
                endTime,
                `"${description.replace(/"/g, '""')}"`
            ]);
        });

        let csvContent = csvRows.map(e => e.join(',')).join('\r\n');
        let blob = new Blob([csvContent], { type: 'text/csv' });
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'calendar.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
</script>
</x-app-layout>