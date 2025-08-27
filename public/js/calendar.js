let currentDate = new Date();
let tasks = [];
let currentView = 'month'; // 'month', 'week', or 'day'

function initializeCalendar() {
    updateCalendarHeader();
    renderCalendar();
    loadTasksForCalendar();
    setupViewSwitcher();
}

function setupViewSwitcher() {
    const viewSwitcher = document.createElement('div');
    viewSwitcher.className = 'flex space-x-2 mb-4';
    viewSwitcher.innerHTML = `
        <button onclick="switchView('month')" class="px-3 py-1 rounded ${currentView === 'month' ? 'bg-blue-500 text-white' : 'bg-gray-200'}">Month</button>
        <button onclick="switchView('week')" class="px-3 py-1 rounded ${currentView === 'week' ? 'bg-blue-500 text-white' : 'bg-gray-200'}">Week</button>
        <button onclick="switchView('day')" class="px-3 py-1 rounded ${currentView === 'day' ? 'bg-blue-500 text-white' : 'bg-gray-200'}">Day</button>
    `;
    document.querySelector('.calendar-header').appendChild(viewSwitcher);
}

function switchView(view) {
    currentView = view;
    
    // Hide all views
    document.getElementById('monthView').classList.add('hidden');
    document.getElementById('weekView').classList.add('hidden');
    document.getElementById('dayView').classList.add('hidden');
    
    // Show selected view
    document.getElementById(`${view}View`).classList.remove('hidden');
    
    updateCalendarHeader();
    renderCalendar();
    setupViewSwitcher();
}

function updateCalendarHeader() {
    const monthYearDisplay = document.getElementById('monthYearDisplay');
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    
    if (currentView === 'month') {
        monthYearDisplay.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    } else if (currentView === 'week') {
        const weekStart = new Date(currentDate);
        weekStart.setDate(currentDate.getDate() - currentDate.getDay());
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 6);
        monthYearDisplay.textContent = `${monthNames[weekStart.getMonth()]} ${weekStart.getDate()} - ${monthNames[weekEnd.getMonth()]} ${weekEnd.getDate()}, ${weekStart.getFullYear()}`;
    } else {
        monthYearDisplay.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getDate()}, ${currentDate.getFullYear()}`;
    }
}

function renderCalendar() {
    const calendarDays = document.getElementById('calendarDays');
    calendarDays.innerHTML = '';

    if (currentView === 'month') {
        renderMonthView();
    } else if (currentView === 'week') {
        renderWeekView();
    } else {
        renderDayView();
    }
}

function renderMonthView() {
    const calendarDays = document.getElementById('calendarDays');
    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const startingDay = firstDay.getDay();
    const totalDays = lastDay.getDate();

    // Add empty cells for days before the first day of the month
    for (let i = 0; i < startingDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'h-24 border border-gray-200 dark:border-gray-700';
        calendarDays.appendChild(emptyCell);
    }

    // Add cells for each day of the month
    for (let day = 1; day <= totalDays; day++) {
        const cell = document.createElement('div');
        cell.className = 'h-24 border border-gray-200 dark:border-gray-700 relative';
        cell.setAttribute('data-date', `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`);

        // Add day number
        const dayNumber = document.createElement('span');
        dayNumber.className = 'absolute top-1 left-1 text-sm font-medium text-gray-700 dark:text-gray-300';
        dayNumber.textContent = day;
        cell.appendChild(dayNumber);

        // Add task container
        const taskContainer = document.createElement('div');
        taskContainer.className = 'mt-6 px-1 space-y-1 overflow-y-auto max-h-16';
        cell.appendChild(taskContainer);

        // Add quick task button
        const addTaskBtn = document.createElement('button');
        addTaskBtn.className = 'absolute bottom-1 right-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300';
        addTaskBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>';
        addTaskBtn.onclick = (e) => {
            e.stopPropagation();
            openQuickTaskModal(cell.getAttribute('data-date'));
        };
        cell.appendChild(addTaskBtn);

        calendarDays.appendChild(cell);
    }

    updateCalendarEvents();
}

function renderWeekView() {
    const calendarDays = document.getElementById('calendarDays');
    const weekStart = new Date(currentDate);
    weekStart.setDate(currentDate.getDate() - currentDate.getDay());
    
    // Create time slots for each day
    for (let i = 0; i < 7; i++) {
        const day = new Date(weekStart);
        day.setDate(weekStart.getDate() + i);
        
        const dayColumn = document.createElement('div');
        dayColumn.className = 'border border-gray-200 dark:border-gray-700';
        dayColumn.setAttribute('data-date', day.toISOString().split('T')[0]);
        
        // Add day header
        const dayHeader = document.createElement('div');
        dayHeader.className = 'p-2 border-b border-gray-200 dark:border-gray-700';
        dayHeader.innerHTML = `
            <div class="font-semibold">${day.toLocaleDateString('en-US', { weekday: 'short' })}</div>
            <div class="text-sm">${day.getDate()}</div>
        `;
        dayColumn.appendChild(dayHeader);
        
        // Add time slots
        for (let hour = 0; hour < 24; hour++) {
            const timeSlot = document.createElement('div');
            timeSlot.className = 'h-12 border-b border-gray-100 dark:border-gray-700 relative';
            timeSlot.setAttribute('data-time', `${String(hour).padStart(2, '0')}:00`);
            dayColumn.appendChild(timeSlot);
        }
        
        calendarDays.appendChild(dayColumn);
    }
    
    updateCalendarEvents();
}

function renderDayView() {
    const calendarDays = document.getElementById('calendarDays');
    const dayColumn = document.createElement('div');
    dayColumn.className = 'border border-gray-200 dark:border-gray-700';
    dayColumn.setAttribute('data-date', currentDate.toISOString().split('T')[0]);
    
    // Add day header
    const dayHeader = document.createElement('div');
    dayHeader.className = 'p-2 border-b border-gray-200 dark:border-gray-700';
    dayHeader.innerHTML = `
        <div class="font-semibold">${currentDate.toLocaleDateString('en-US', { weekday: 'long' })}</div>
        <div class="text-sm">${currentDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</div>
    `;
    dayColumn.appendChild(dayHeader);
    
    // Add time slots
    for (let hour = 0; hour < 24; hour++) {
        const timeSlot = document.createElement('div');
        timeSlot.className = 'h-16 border-b border-gray-100 dark:border-gray-700 relative';
        timeSlot.setAttribute('data-time', `${String(hour).padStart(2, '0')}:00`);
        
        // Add time label
        const timeLabel = document.createElement('div');
        timeLabel.className = 'absolute left-0 top-0 text-xs text-gray-500 p-1';
        timeLabel.textContent = `${hour}:00`;
        timeSlot.appendChild(timeLabel);
        
        dayColumn.appendChild(timeSlot);
    }
    
    calendarDays.appendChild(dayColumn);
    updateCalendarEvents();
}

function changeMonth(delta) {
    if (currentView === 'month') {
        currentDate.setMonth(currentDate.getMonth() + delta);
    } else if (currentView === 'week') {
        currentDate.setDate(currentDate.getDate() + (delta * 7));
    } else {
        currentDate.setDate(currentDate.getDate() + delta);
    }
    updateCalendarHeader();
    renderCalendar();
}

function loadTasksForCalendar() {
    const projectId = document.getElementById('projectOverviewSection').getAttribute('data-project-id');
    fetch(`/projects/${projectId}/tasks`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            tasks = data.tasks || [];
            updateCalendarEvents();
        })
        .catch(error => {
            console.error('Error loading tasks:', error);
        });
}

function updateCalendarEvents() {
    const cells = document.getElementById('calendarDays').children;
    const currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();

    // Clear existing task displays
    Array.from(cells).forEach(cell => {
        const taskContainer = cell.querySelector('div:not(:first-child)');
        if (taskContainer) taskContainer.innerHTML = '';
    });

    // Add tasks to calendar
    tasks.forEach(task => {
        const taskDate = new Date(task.due_date);
        if (currentView === 'month') {
            if (taskDate.getMonth() === currentMonth && taskDate.getFullYear() === currentYear) {
                const dayIndex = taskDate.getDate() - 1 + new Date(currentYear, currentMonth, 1).getDay();
                const cell = cells[dayIndex];
                if (cell) {
                    const taskContainer = cell.querySelector('div:not(:first-child)');
                    if (taskContainer) {
                        addTaskToCell(task, taskContainer);
                    }
                }
            }
        } else {
            // For week and day views, find matching time slots
            const matchingCells = Array.from(cells).filter(cell => 
                cell.getAttribute('data-date') === taskDate.toISOString().split('T')[0]
            );
            
            matchingCells.forEach(cell => {
                const taskContainer = cell.querySelector('div:not(:first-child)');
                if (taskContainer) {
                    addTaskToCell(task, taskContainer);
                }
            });
        }
    });
}

function addTaskToCell(task, container) {
    const taskElement = document.createElement('div');
    taskElement.className = `task-item ${getTaskColorClass(task.priority)}`;
    taskElement.textContent = task.task_name;
    taskElement.title = `Due: ${formatDate(task.due_date)}\nPriority: ${task.priority}\nStatus: ${task.status}\nAssigned to: ${task.user ? task.user.name : 'Unassigned'}`;
    taskElement.onclick = () => openTaskDetailsFromCalendar(task);
    taskElement.draggable = true;
    taskElement.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', JSON.stringify(task));
    });
    container.appendChild(taskElement);
}

function getTaskColorClass(priority) {
    switch (priority) {
        case 'High':
            return 'task-priority-high';
        case 'Normal':
            return 'task-priority-normal';
        case 'Low':
            return 'task-priority-low';
        default:
            return 'task-priority-normal';
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function openQuickTaskModal(date) {
    // Create modal for quick task creation
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-96">
            <h3 class="text-lg font-semibold mb-4">Add New Task</h3>
            <form id="quickTaskForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Task Name</label>
                    <input type="text" name="taskName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                    <input type="date" name="dueDate" value="${date}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                    <select name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="High">High</option>
                        <option value="Normal">Normal</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Create</button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    modal.querySelector('form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const taskData = {
            task_name: formData.get('taskName'),
            due_date: formData.get('dueDate'),
            priority: formData.get('priority'),
            status: 'Pending'
        };
        
        // Here you would typically make an API call to create the task
        console.log('Creating task:', taskData);
        modal.remove();
    });
}

function openTaskDetailsFromCalendar(task) {
    console.log('Task details:', task);
    // Implement task details modal or navigation
}