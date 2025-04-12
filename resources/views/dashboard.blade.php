<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                    {{ __("Project Overview") }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Total Projects -->
                    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow cursor-pointer" onclick="showProjects('total')">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Total Projects</h4>
                        <p class="text-4xl text-center font-bold text-gray-800 dark:text-gray-200">{{ $totalProjects }}</p>
                    </div>
                
                    <!-- In Progress Projects -->
                    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow cursor-pointer" onclick="showProjects('inProgress')">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">In Progress</h4>
                        <p class="text-4xl text-center font-bold text-gray-800 dark:text-gray-200">{{ $inProgressProjects }}</p>
                    </div>
                
                    <!-- Completed Projects -->
                    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow cursor-pointer" onclick="showProjects('completed')">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Completed</h4>
                        <p class="text-4xl text-center font-bold text-gray-800 dark:text-gray-200">{{ $completedProjects }}</p>
                    </div>
                
                    <!-- Delayed Projects -->
                    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow cursor-pointer" onclick="showProjects('delayed')">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Delayed</h4>
                        <p class="text-4xl text-center font-bold text-gray-800 dark:text-gray-200">{{ $delayedProjects }}</p>
                    </div>
                </div>
                    <!-- Modal -->
                    <div id="projectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden" onclick="closeModalOnOutsideClick(event)">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-1/2 opacity-0 scale-95 transition transform duration-300 relative" onclick="event.stopPropagation()">
                            <!-- Close Button -->
                            <button onclick="closeModal()" class="absolute top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 focus:outline-none">
                                &times;
                            </button>
                            
                            <!-- Modal Title -->
                            <h3 id="modalTitle" class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4"></h3>
                            
                            <!-- Styled List -->
                            <ul id="modalContent" class="list-disc pl-5 space-y-4 text-lg text-gray-800 dark:text-gray-200">
                                <!-- List items will be dynamically added here -->
                            </ul>
                        </div>
                    </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                    <!-- Sales Chart -->
                    <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow col-span-2">
                        <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Sales Chart</h4>
                        <canvas id="myChart" width="400" height="200"></canvas>
                    </div>

                    <!-- Project Progress Chart -->
                    <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow col-span-1">
                        <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Project Progress</h4>
                        <canvas id="progressChart" width="400" height="200"></canvas>
                    </div>

                    <!-- Team Performance Chart -->
                    <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow col-span-1">
                        <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Team Performance</h4>
                        <canvas id="teamPerformanceChart" width="400" height="200"></canvas>
                    </div>

                    <!-- Resource Allocation Chart -->
                    <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow col-span-2">
                        <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Resource Allocation</h4>
                        <canvas id="resourceChart" width="400" height="200"></canvas>
                    </div>

                    <!-- Timeline Chart -->
                    <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow col-span-2">
                        <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Timeline</h4>
                        <canvas id="timelineChart" width="400" height="200"></canvas>
                    </div>

                    <!-- Burndown Chart -->
                    <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow col-span-4">
                        <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Burndown Chart</h4>
                        <canvas id="burndownChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    const projects = @json($projectsByStatus);

    function showProjects(status) {
        const modal = document.getElementById('projectModal');
        const content = modal.querySelector('div'); // Target the modal content
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');

        // Set modal title and content
        modalTitle.textContent = `${status.charAt(0).toUpperCase() + status.slice(1)} Projects`;
        modalContent.innerHTML = projects[status].map(project => `<li>${project.proj_name}</li>`).join('');

        // Show modal with animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('projectModal');
        const content = modal.querySelector('div'); // Target the modal content

        // Hide modal with animation
        content.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function closeModalOnOutsideClick(event) {
        const modal = document.getElementById('projectModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Sales Chart -->
<script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April'],
            datasets: [{
                label: 'Sales',
                data: [10, 20, 30, 40],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        color: 'white' // Set legend text color to white
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: 'white' // Set x-axis text color to white
                    }
                },
                y: {
                    ticks: {
                        color: 'white' // Set y-axis text color to white
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>

<!-- Project Progress Chart -->
<script>
    const progressCtx = document.getElementById('progressChart').getContext('2d');
    const progressChart = new Chart(progressCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($projectProgressData)) !!},
            datasets: [{
                data: {!! json_encode(array_values($projectProgressData)) !!}, 
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(255, 99, 132, 0.6)'  
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        color: 'white' // Set legend text color to white
                    }
                }
            },
        }
    });
</script>

<!-- Team Performance Chart -->
<script>
    const teamCtx = document.getElementById('teamPerformanceChart').getContext('2d');
    const teamPerformanceChart = new Chart(teamCtx, {
        type: 'bar',
        data: {
            labels: ['Alice', 'Bob', 'Charlie', 'Diana'],
            datasets: [{
                label: 'Tasks Completed',
                data: [12, 19, 8, 15],
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        color: 'white' // Legend text color
                    }
                }
            },
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        color: 'white' // X-axis tick labels
                    },
                    grid: {
                        color: 'gray' // Optional: set grid line color
                    }
                },
                y: {
                    ticks: {
                        color: 'white' // Y-axis tick labels
                    },
                    grid: {
                        color: 'gray' // Optional: set grid line color
                    }
                }
            }
        }
    });
</script>


<!-- Resource Allocation Chart -->
<script>
    const resourceCtx = document.getElementById('resourceChart').getContext('2d');
    const resourceChart = new Chart(resourceCtx, {
        type: 'pie',
        data: {
            labels: ['Project A', 'Project B', 'Project C'],
            datasets: [{
                data: [40, 35, 25],
                backgroundColor: [
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                    'rgba(75, 192, 192, 0.6)'
                ],
                borderColor: [
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        color: 'white' // Set legend text to white
                    }
                },
                tooltip: {
                    titleColor: 'white',
                    bodyColor: 'white'
                }
            },
            responsive: true
        }
    });
</script>

<!-- Timeline Chart -->
<script>
    const timelineCtx = document.getElementById('timelineChart').getContext('2d');
    const timelineChart = new Chart(timelineCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Tasks Completed',
                data: [5, 10, 15, 20],
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: 'white' 
                    }
                },
                tooltip: {
                    titleColor: 'white',
                    bodyColor: 'white'
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: 'white' 
                    },
                    grid: {
                        color: 'gray', 
                        borderColor: 'gray' 
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: 'white'
                    },
                    grid: {
                        color: 'gray', 
                        borderColor: 'gray'
                    }
                }
            }
        }
    });
</script>


<!-- Burndown Chart -->
<script>
    const burndownCtx = document.getElementById('burndownChart').getContext('2d');
    const burndownChart = new Chart(burndownCtx, {
        type: 'line',
        data: {
            labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5'],
            datasets: [{
                label: 'Remaining Tasks',
                data: [20, 15, 10, 5, 0],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: 'white'
                    }
                },
                tooltip: {
                    titleColor: 'white',
                    bodyColor: 'white'
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: 'white'
                    },
                    grid: {
                        color: 'gray', 
                        borderColor: 'gray' 
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: 'white'
                    },
                    grid: {
                        color: 'gray', 
                        borderColor: 'gray' 
                    }
                }
            }
        }
    });
</script>

