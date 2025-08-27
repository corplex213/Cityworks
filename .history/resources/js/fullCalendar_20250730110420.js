import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
let currentTaskFilter = 'all';
let currentUserFilter = 'mine';

function updateTaskFilterButtons() {
    document.querySelectorAll('.task-filter').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600');
        btn.classList.add('bg-slate-600');
        if (btn.dataset.filter === currentTaskFilter) {
            btn.classList.add('active', 'bg-blue-600');
            btn.classList.remove('bg-slate-600');
        }
    });
}

function formatDateRange(start, end) {
    // Format as "May 28, 2025" or "May 28, 2025 - May 29, 2025"
    const opts = { year: 'numeric', month: 'short', day: 'numeric' };
    const startDate = new Date(start);
    const endDate = new Date(end);
    if (start && end && start !== end) {
        return `${startDate.toLocaleDateString(undefined, opts)} - ${endDate.toLocaleDateString(undefined, opts)}`;
    } else if (start) {
        return startDate.toLocaleDateString(undefined, opts);
    }
    return '';
}

function formatEventDateTime(start, end) {
    // Format as "May 28, 2025, 2:00 PM - 2:30 PM"
    const dateOpts = { year: 'numeric', month: 'short', day: 'numeric' };
    const timeOpts = { hour: 'numeric', minute: '2-digit', hour12: true };
    const startDate = new Date(start);
    const endDate = new Date(end);
    if (start && end) {
        return `${startDate.toLocaleDateString(undefined, dateOpts)}, ${startDate.toLocaleTimeString(undefined, timeOpts)} - ${endDate.toLocaleTimeString(undefined, timeOpts)}`;
    } else if (start) {
        return `${startDate.toLocaleDateString(undefined, dateOpts)}, ${startDate.toLocaleTimeString(undefined, timeOpts)}`;
    }
    return '';
}

function openCreateTaskModal() {
        const modal = document.getElementById('create-task-modal');
        const content = document.getElementById('createTaskContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }
function closeCreateTaskModal() {
        const modal = document.getElementById('create-task-modal');
        const content = document.getElementById('createTaskContent');
        content.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
document.getElementById('open-create-task-modal')?.addEventListener('click', openCreateTaskModal);
document.getElementById('close-create-task-modal-btn')?.addEventListener('click', function() {
    closeCreateTaskModal();
});

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar-root');
    let calendar;

    updateTaskFilterButtons();

    // --- Populate user filter dropdown ---
    fetch('/calendar/users')
        .then(res => res.json())
        .then(users => {
            const userFilter = document.getElementById('user-filter');
            if (userFilter) {
                // Remove any user options except 'mine' and 'all'
                userFilter.innerHTML = `
                    <option value="mine">Only mine</option>
                    <option value="all">All users</option>
                `;
                users.forEach(user => {
                    userFilter.innerHTML += `<option value="${user.id}">${user.name}</option>`;
                });
            }
        });
    document.getElementById('user-filter')?.addEventListener('change', function() {
        currentUserFilter = this.value;
        if (window.calendar) {
            window.calendar.refetchEvents();
        }
    });

    function initializeCalendar() {
        if (!calendarEl) return;
        
        calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin],
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            events: function(info, successCallback, failureCallback) {
                const url = new URL('/calendar/events', window.location.origin);
                url.searchParams.append('start', info.startStr);
                url.searchParams.append('end', info.endStr);
                url.searchParams.append('task_filter', currentTaskFilter);
                url.searchParams.append('userFilter', currentUserFilter);
                
                fetch(url)
                    .then(res => {
                        if (!res.ok) {
                            throw new Error(`HTTP error ${res.status}`);
                        }
                        return res.json();
                    })
                    .then(events => {
                        // Ensure all task events have the correct ID prefix
                        const mapped = events.map(ev => {
                            if (ev.className === 'calendar-task' && !String(ev.id).startsWith('task-')) {
                                ev.id = 'task-' + ev.id;
                            }
                            return ev;
                        });
                        successCallback(mapped);
                        updateSidebar(mapped);
                    })
                    .catch(err => {
                        console.error('Error fetching events:', err);
                        failureCallback(err);
                    });
            },
            eventDidMount: function(info) {
                // Enhanced tooltip with more task details
                const tooltip = `
                    <div class="event-tooltip">
                        <strong>${info.event.title}</strong>
                        <div class="mt-2">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Assigned to: ${info.event.extendedProps.assigned_to}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Priority: ${info.event.extendedProps.priority}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Status: ${info.event.extendedProps.status}</span>
                            </div>
                            ${info.event.extendedProps.description ? `
                            <div class="mt-2 text-sm text-gray-300">
                                ${info.event.extendedProps.description}
                            </div>
                            ` : ''}
                        </div>
                    </div>
                `;
                
                // Add tooltip here if needed
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();

                window.selectedEventId = info.event.id;

                // Common fields
                document.getElementById('modal-title').textContent = info.event.title;

                // Date formatting
                const start = info.event.start;
                const end = info.event.end ? new Date(info.event.end.getTime() - 86400000) : null;
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                let dateStr = '';
                if (start && end && start.toDateString() !== end.toDateString()) {
                    dateStr = `${start.toLocaleDateString(undefined, options)} - ${end.toLocaleDateString(undefined, options)}`;
                } else if (start) {
                    dateStr = start.toLocaleDateString(undefined, options);
                }
                document.getElementById('modal-date').textContent = dateStr;

                // Detect type
                const isEvent = info.event.classNames.includes('calendar-event');
                const statusDot = document.getElementById('modal-status-dot');
                statusDot.className = 'w-3 h-3 rounded-full mr-2';

                if (isEvent) {
                    // EVENT MODAL
                    statusDot.classList.add('bg-gray-400');
                    document.getElementById('modal-project').textContent = '—'; // or blank
                    document.getElementById('modal-priority').textContent = '—';
                    document.getElementById('modal-assigned').textContent = info.event.extendedProps.assigned_to || 'Unassigned';
                } else {
                    // TASK MODAL
                    // Status dot color
                    const status = info.event.extendedProps.status || '';
                    if (status === 'Completed') statusDot.classList.add('bg-green-500');
                    else if (status === 'For Checking') statusDot.classList.add('bg-blue-500');
                    else if (status === 'For Revision') statusDot.classList.add('bg-yellow-400');
                    else if (status === 'Deferred') statusDot.classList.add('bg-red-500');
                    else statusDot.classList.add('bg-gray-400');

                    document.getElementById('modal-project').textContent = info.event.extendedProps.project || 'No project';
                    document.getElementById('modal-priority').textContent = info.event.extendedProps.priority || 'No priority';
                    document.getElementById('modal-assigned').textContent = info.event.extendedProps.assigned_to || 'Unassigned';
                }

                document.getElementById('event-modal').classList.remove('hidden');
            },
            // Add specific styling for calendar events
            
            height: 'auto',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false
            },
            // Additional calendar features
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            weekends: true,
            nowIndicator: true,
            businessHours: {
                daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday
                startTime: '09:00',
                endTime: '17:00',
            },
            slotMinTime: '08:00:00',
            slotMaxTime: '20:00:00',
            allDaySlot: true,
            slotDuration: '00:30:00',
            slotLabelInterval: '01:00',
            expandRows: true,
            stickyHeaderDates: true,
            dayHeaderFormat: { weekday: 'long' },
            views: {
                timeGridWeek: {
                    dayHeaderFormat: { weekday: 'long', day: 'numeric' }
                },
                timeGridDay: {
                    dayHeaderFormat: { weekday: 'long', day: 'numeric', month: 'long' }
                }
            }
        });
        
        window.calendar = calendar;
        calendar.render();
    }

    initializeCalendar();
    window.Echo.channel('calendar-events')
    .listen('CalendarEventCreated', (e) => {
        if (window.calendar) {
            window.calendar.addEvent({
                id: 'event-' + e.id,
                title: e.title,
                start: e.start,
                end: e.end,
                className: 'calendar-event',
                extendedProps: {
                    description: e.description,
                    assigned_to: '', // Add more fields if needed
                    status: null,
                    project: null,
                    priority: null,
                }
            });
        }
    });
    // Add this listener for deletions right after the above
    window.Echo.channel('calendar-events')
        .listen('CalendarEventDeleted', (e) => {
            if (window.calendar) {
                const event = window.calendar.getEventById('event-' + e.id);
                if (event) {
                    event.remove();
                }
            }
        });
    // Listen for real-time task assignments
    window.Echo.channel('tasks')
    .listen('TaskAssigned', (e) => {
        if (window.calendar) {
            // Main task
            const eventId = 'task-' + e.id;
            const existing = window.calendar.getEventById(eventId);

            if (existing) {
                existing.setProp('title', e.title);
                existing.setStart(e.start);
                existing.setEnd(e.end);
                existing.setProp('classNames', [e.className]);
                existing.setExtendedProp('status', e.status);
                existing.setExtendedProp('priority', e.priority);
                existing.setExtendedProp('assigned_to', e.assigned_to);
                existing.setExtendedProp('project', e.project);
            } else {
                window.calendar.addEvent({
                    id: eventId,
                    title: e.title,
                    start: e.start,
                    end: e.end,
                    className: e.className,
                    extendedProps: {
                        assigned_to: e.assigned_to,
                        status: e.status,
                        priority: e.priority,
                        project: e.project,
                    }
                });
            }

            // Subtasks
            if (Array.isArray(e.subtasks)) {
                e.subtasks.forEach(sub => {
                    const subId = 'task-' + sub.id;
                    const subExisting = window.calendar.getEventById(subId);
                    if (subExisting) {
                        subExisting.setProp('title', sub.title);
                        subExisting.setStart(sub.start);
                        subExisting.setEnd(sub.end);
                        subExisting.setProp('classNames', [sub.className]);
                        subExisting.setExtendedProp('status', sub.status);
                        subExisting.setExtendedProp('priority', sub.priority);
                        subExisting.setExtendedProp('assigned_to', sub.assigned_to);
                        subExisting.setExtendedProp('project', sub.project);
                    } else {
                        window.calendar.addEvent({
                            id: subId,
                            title: sub.title,
                            start: sub.start,
                            end: sub.end,
                            className: sub.className,
                            extendedProps: {
                                assigned_to: sub.assigned_to,
                                status: sub.status,
                                priority: sub.priority,
                                project: sub.project,
                            }
                        });
                    }
                });
            }
        }
    });

    function updateSidebar(events) {
        const list = document.getElementById('user-task-list');
        const completedList = document.getElementById('completed-task-list');
        const completedCount = document.getElementById('completed-count');
        const deferredList = document.getElementById('deferred-task-list');
        const deferredCount = document.getElementById('deferred-count');
        const eventList = document.getElementById('user-event-list');
        const ongoingCount = document.getElementById('ongoing-count');
        const eventCount = document.getElementById('event-count');
        
        if (!list && !completedList && !eventList) {
            console.warn('Sidebar elements not found in DOM');
            return;
        }
        
        const myEvents = events.filter(ev => ev.className === 'calendar-event');
        
        // Fetch all tasks (including completed) for the sidebar
        fetch('/calendar/events?all=1')
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error ${res.status}`);
                }
                return res.json();
            })
            .then(allEvents => {
                const activeTasks = allEvents.filter(ev =>
                    ev.className !== 'calendar-event' &&
                    ev.extendedProps.status !== 'Completed' &&
                    ev.extendedProps.status !== 'Deferred'
                );
                const completedTasks = allEvents.filter(ev =>
                    ev.className !== 'calendar-event' &&
                    ev.extendedProps.status === 'Completed'
                );
                const deferredTasks = allEvents.filter(ev =>
                    ev.className !== 'calendar-event' &&
                    ev.extendedProps.status === 'Deferred'
                );

                // --- ACTIVE TASKS (match fullCalendar.js style) ---
                if (list) {
                    list.innerHTML = activeTasks.length
                    ? activeTasks.map(ev => `
                        <li class="group flex items-start justify-between p-2 rounded hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-5 h-5 text-blue-700 dark:text-blue-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <a href="#" class="font-medium text-blue-700 dark:text-blue-300 hover:underline truncate" title="${ev.title}">${ev.title}</a>
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">Activity: ${ev.extendedProps.project || 'No activity'}</div>
                                <div class="text-xs text-gray-400">${ev.extendedProps.status}</div>
                                <div class="text-xs text-gray-400">
                                    ${formatDateRange(ev.start, ev.end)}
                                </div>
                            </div>
                        </li>
                    `).join('')
                    : `<li class="text-center text-gray-400 py-4">No Current Tasks</li>`;
                }

                if (ongoingCount) ongoingCount.textContent = activeTasks.length;
                if (eventCount) eventCount.textContent = myEvents.length;
                if (completedList && completedCount) {
                    renderCompletedTasks(completedTasks);
                }
                if (deferredList && deferredCount) {
                    renderDeferredTasks(deferredTasks);
                }
                
                // --- EVENTS (keep your style) ---
                if (eventList) {
                    eventList.innerHTML = myEvents.length
                        ? myEvents.map(ev => `
                            <li class="group flex items-start justify-between p-2 rounded hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-5 h-5 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                                        </svg>
                                        <a href="#" class="font-medium text-indigo-400 hover:underline truncate" title="${ev.title}">${ev.title}</a>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">${formatEventDateTime(ev.start, ev.end)}</div>
                                    <div class="text-xs text-gray-400">${ev.extendedProps.description || ''}</div>
                                </div>
                            </li>
                        `).join('')
                        : `<li class="text-center text-gray-400 py-4">No Events</li>`;
                }
            })
            .catch(err => {
                console.error('Error updating sidebar:', err);
            });
    }

    function renderCompletedTasks(tasks) {
        const completedList = document.getElementById('completed-task-list');
        const completedCount = document.getElementById('completed-count');
        
        if (completedCount) {
            completedCount.textContent = tasks.length;
        }
        
        if (completedList) {
            completedList.innerHTML = tasks.length
                ? tasks.map(ev => `
                    <li class="group flex items-start justify-between p-2 rounded hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-1">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                                <a href="#" class="font-medium text-blue-700 dark:text-blue-300 hover:underline truncate line-through" title="${ev.title}">${ev.title}</a>
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">Completed: ${new Date(ev.start).toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' })}</div>
                        </div>
                    </li>
                `).join('')
                : `<li class="text-center text-gray-400 py-4">No Completed Tasks</li>`;
        }
    }

    function renderDeferredTasks(tasks) {
        const deferredList = document.getElementById('deferred-task-list');
        const deferredCount = document.getElementById('deferred-count');
        
        if (deferredCount) {
            deferredCount.textContent = tasks.length;
        }
        
        if (deferredList) {
            deferredList.innerHTML = tasks.length
                ? tasks.map(ev => `
                    <li class="group flex items-start justify-between p-2 rounded hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-1">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <a href="#" class="font-medium text-red-700 dark:text-red-400 hover:underline truncate" title="${ev.title}">${ev.title}</a>
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">Deferred: ${new Date(ev.start).toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' })}</div>
                        </div>
                    </li>
                `).join('')
                : `<li class="text-center text-gray-400 py-4">No Deferred Tasks</li>`;
        }
    }

    // Set up filter button handlers
    document.querySelectorAll('.task-filter').forEach(button => {
        button.addEventListener('click', function() {
            currentTaskFilter = this.dataset.filter;
            updateTaskFilterButtons();
            if (window.calendar) {
                window.calendar.refetchEvents();
            }
        });
    });
    
    // Set up toggle handlers for sidebar sections
    const toggleDeferredBtn = document.getElementById('toggle-deferred-tasks');
    const deferredChevron = document.getElementById('deferred-chevron');
    const deferredList = document.getElementById('deferred-task-list');
    if (toggleDeferredBtn && deferredList && deferredChevron) {
        toggleDeferredBtn.addEventListener('click', () => {
            deferredList.classList.toggle('hidden');
            deferredChevron.classList.toggle('rotate-90');
        });
    }
    
    const toggleBtn = document.getElementById('toggle-completed-tasks');
    const chevron = document.getElementById('completed-chevron');
    const completedList = document.getElementById('completed-task-list');
    if (toggleBtn && completedList && chevron) {
        // Hide completed by default
        completedList.classList.add('hidden');
        chevron.classList.add('rotate-90');
        
        toggleBtn.addEventListener('click', () => {
            completedList.classList.toggle('hidden');
            chevron.classList.toggle('rotate-90');
        });
    }
    
    const toggleMyTasksBtn = document.getElementById('toggle-my-tasks');
    const myTasksChevron = document.getElementById('mytasks-chevron');
    const list = document.getElementById('user-task-list');
    if (toggleMyTasksBtn && list && myTasksChevron) {
        toggleMyTasksBtn.addEventListener('click', () => {
            list.classList.toggle('hidden');
            myTasksChevron.classList.toggle('rotate-90');
        });
    }
    
    const toggleMyEventsBtn = document.getElementById('toggle-my-events');
    const myEventsChevron = document.getElementById('myevents-chevron');
    const eventList = document.getElementById('user-event-list');
    if (toggleMyEventsBtn && eventList && myEventsChevron) {
        // Hide events by default
        eventList.classList.add('hidden');
        myEventsChevron.classList.add('rotate-90');
        
        toggleMyEventsBtn.addEventListener('click', () => {
            eventList.classList.toggle('hidden');
            myEventsChevron.classList.toggle('rotate-90');
        });
    }

    // Modal event handlers
    document.getElementById('close-modal')?.addEventListener('click', function() {
        document.getElementById('event-modal').classList.add('hidden');
    });
    
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('event-modal');
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
    
    document.getElementById('open-create-task-modal')?.addEventListener('click', function() {
        document.getElementById('create-task-modal').classList.remove('hidden');
    });
    
    document.getElementById('close-create-task-modal')?.addEventListener('click', function() {
        document.getElementById('create-task-modal').classList.add('hidden');
    });
    
    document.getElementById('create-task-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const date = form.date.value;
        const startTime = form.start_time.value;
        const endTime = form.end_time.value;
        const errorDiv = document.getElementById('create-task-error');

        // Combine date and time into ISO strings
        const start = `${date}T${startTime}`;
        const end = `${date}T${endTime}`;

        // Check if start time is after end time
        if (startTime && endTime && startTime > endTime) {
            errorDiv.textContent = "Start time cannot be later than end time.";
            errorDiv.classList.remove('hidden');
            return;
        } else {
            errorDiv.classList.add('hidden');
        }

        fetch('/calendar/events', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                title: form.task_name.value,
                start: start,
                end: end,
                description: form.description.value
            })
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error ${res.status}`);
            }
            return res.json();
        })
        .then(_event => {
            document.getElementById('create-task-modal').classList.add('hidden');
            document.getElementById('create-task-form').reset();
        })
        .catch(err => {
            errorDiv.textContent = "Error creating event.";
            errorDiv.classList.remove('hidden');
            console.error('Error creating event:', err);
        });
    });

    document.getElementById('delete-event-btn')?.addEventListener('click', function() {
        if (!window.selectedEventId) return;
        if (!confirm('Are you sure you want to delete this event?')) return;

        // Remove 'event-' prefix if present
        let id = window.selectedEventId.startsWith('event-') ? window.selectedEventId.replace('event-', '') : window.selectedEventId;

        fetch(`/calendar/events/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to delete event');
            document.getElementById('event-modal').classList.add('hidden');
            if (window.calendar) window.calendar.refetchEvents();
        })
        .catch(err => {
            alert('Error deleting event.');
            console.error(err);
        });
    });
});