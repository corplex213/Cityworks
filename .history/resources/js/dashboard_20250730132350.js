import Chart from 'chart.js/auto';
import '../css/dashboard.css';
console.log('Script loaded');

document.addEventListener('DOMContentLoaded', function() {
        tippy('#total-projects-card', {
            content: 'Total number of activities in the system, across all statuses. The percentage shows the change in new activities added this week compared to last week.',
            placement: 'top',
            theme: 'light-border',
            animation: 'scale',
            arrow: true,
            delay: [100, 100],
        });
        tippy('#in-progress-card', {
            content: 'Activities that are in progress. The percentage shows how the number changed this week compared to last week.',
            placement: 'top',
            theme: 'light-border',
            animation: 'scale',
            arrow: true,
            delay: [100, 100],
        });
        tippy('#completed-card', {
            content: 'Activities that are completed. The percentage shows how the number changed this week compared to last week.',
            placement: 'top',
            theme: 'light-border',
            animation: 'scale',
            arrow: true,
            delay: [100, 100],
        });
        tippy('#deferred-card', {
            content: 'Activities that are on hold or delayed. The percentage shows how the number changed this week compared to last week.',
            placement: 'top',
            theme: 'light-border',
            animation: 'scale',
            arrow: true,
            delay: [100, 100],
        });
        tippy('#recent-activities-header', {
            content: 'Shows the latest important actions performed in the system, such as project updates, deletions, and event changes.',
            placement: 'top',
            theme: 'light-border',
            animation: 'scale',
            arrow: true,
            delay: [100, 100],
        });
        tippy('#upcoming-deadlines', {
            content: 'This section lists tasks with deadlines approaching soon, helping you prioritize urgent work.',
            placement: 'top',
            theme: 'light-border',
            animation: 'scale',
            arrow: true,
            delay: [100, 100],
        });
            // Task Trends Chart
            if (window.dashboardData && window.dashboardData.taskTrends) {
                const ctx = document.getElementById('taskTrendsChart');
                if (ctx) {
                    const data = window.dashboardData.taskTrends;
                    const labels = data.map(item => item.week);
                    const created = data.map(item => item.created);
                    const completed = data.map(item => item.completed);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Tasks Created',
                                    data: created,
                                    borderColor: '#3b82f6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                    fill: true,
                                    tension: 0.4,
                                },
                                {
                                    label: 'Tasks Completed',
                                    data: completed,
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                    fill: true,
                                    tension: 0.4,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: true },
                                tooltip: { mode: 'index', intersect: false }
                            },
                            scales: {
                                x: { title: { display: true, text: 'Week' } },
                                y: { title: { display: true, text: 'Tasks' }, beginAtZero: true }
                            }
                        }
                    });
                }
            }
        
            // Project Status Chart
            const projectStatusCtx = document.getElementById('projectStatusChart').getContext('2d');
            let projectStatusChart = new Chart(projectStatusCtx, {
                type: 'pie',
                data: {
                    labels: ['In Progress', 'Completed', 'Deferred'],
                    datasets: [{
                        data: [
                            window.dashboardData.inProgressProjects,
                            window.dashboardData.completedProjects,
                            window.dashboardData.deferredProjects
                        ],
                        backgroundColor: [
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(220, 53, 69, 0.8)'
                        ],
                        borderColor: [
                            'rgba(255, 193, 7, 1)',
                            'rgba(40, 167, 69, 1)',
                            'rgba(220, 53, 69, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 35,
                                color: '#a3a3a3',
                                font: {
                                    size: 13,
                                    weight: '400'
                                }
                            }
                        }
                    }
                }
            });

            // Function to update project status chart
            function updateProjectStatusChart(chartType) {
                projectStatusChart.destroy();

                if (chartType === 'bar') {
                    projectStatusChart = new Chart(projectStatusCtx, {
                        type: 'bar',
                        data: {
                            labels: ['In Progress', 'Completed', 'Deferred'],
                            datasets: [
                                {
                                    label: 'Project Status',
                                    data: [
                                        window.dashboardData.inProgressProjects,
                                        window.dashboardData.completedProjects,
                                        window.dashboardData.deferredProjects
                                    ],
                                    backgroundColor: [
                                        'rgba(255, 193, 7, 0.8)',
                                        'rgba(40, 167, 69, 0.8)',
                                        'rgba(220, 53, 69, 0.8)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 193, 7, 1)',
                                        'rgba(40, 167, 69, 1)',
                                        'rgba(220, 53, 69, 1)'
                                    ],
                                    borderWidth: 1,
                                    borderRadius: 10
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 35,
                                        color: (ctx) => {
                                            const colors = ['#facc15', '#22c55e', '#ef4444'];
                                            return colors[ctx.dataIndex] || '#a3a3a3';
                                        },
                                        font: {
                                            size: 13,
                                            weight: '400'
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    display: false
                                }
                            }
                        }
                    });

                    // Add custom legends if needed
                    const legendContainer = document.getElementById('projectStatusLegend');
                        if (legendContainer) {
                            legendContainer.innerHTML = `
                                <div class="flex space-x-4 justify-center mt-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-4 h-4 rounded bg-yellow-500 inline-block"></span>
                                        <span class="text-sm text-gray-700 dark:text-gray-200">In Progress</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="w-4 h-4 rounded bg-green-500 inline-block"></span>
                                        <span class="text-sm text-gray-700 dark:text-gray-200">Completed</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="w-4 h-4 rounded bg-red-500 inline-block"></span>
                                        <span class="text-sm text-gray-700 dark:text-gray-200">Deferred</span>
                                    </div>
                                </div>
                            `;
                        }
                } else {
                    // Create pie chart
                    projectStatusChart = new Chart(projectStatusCtx, {
                        type: 'pie',
                        data: {
                            labels: ['In Progress', 'Completed', 'Deferred'],
                            datasets: [{
                                data: [
                                    window.dashboardData.inProgressProjects,
                                    window.dashboardData.completedProjects,
                                    window.dashboardData.deferredProjects
                                ],
                                backgroundColor: [
                                    'rgba(255, 193, 7, 0.8)',
                                    'rgba(40, 167, 69, 0.8)',
                                    'rgba(220, 53, 69, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(255, 193, 7, 1)',
                                    'rgba(40, 167, 69, 1)',
                                    'rgba(220, 53, 69, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 35,
                                        color: (ctx) => {
                                            const colors = ['#facc15', '#22c55e', '#ef4444'];
                                            return colors[ctx.dataIndex] || '#a3a3a3';
                                        },
                                        font: {
                                            size: 13,
                                            weight: '400'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Event listener for project status chart type changes
            document.getElementById('projectStatusChartType').addEventListener('change', function(e) {
                updateProjectStatusChart(e.target.value);
            });

            // Project Type Chart
            const projectTypeCtx = document.getElementById('projectTypeChart').getContext('2d');
            let projectTypeChart = new Chart(projectTypeCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(window.dashboardData.projectTypes),
                    datasets: [{
                        label: 'Number of Projects',
                        data: Object.values(window.dashboardData.projectTypes),
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            display:false
                        }
                    }
                }
            });

            // Store project task data
            const projectTaskData = window.dashboardData.projectTaskData;

            // Task Priority Chart
            const taskPriorityCtx = document.getElementById('taskPriorityChart').getContext('2d');
            let taskPriorityChart = new Chart(taskPriorityCtx, {
                type: 'pie',
                data: {
                    labels: ['High', 'Normal', 'Low'],
                    datasets: [{
                        data: [0, 0, 0],
                        backgroundColor: [
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(40, 167, 69, 0.8)'
                        ],
                        borderColor: [
                            'rgba(220, 53, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(40, 167, 69, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    }
                }
            });

            // Task Priority Chart
            const taskPriorityChartType = document.getElementById('taskPriorityChartType');
            taskPriorityChartType.addEventListener('change', function(e) {
                const chartType = e.target.value;
                taskPriorityChart.destroy();
                
                if (chartType === 'bullet') {
                    // Create bullet chart
                    taskPriorityChart = new Chart(taskPriorityCtx, {
                        type: 'bar',
                        data: {
                            labels: ['High', 'Normal', 'Low'],
                            datasets: [
                                {
                                    label: 'Current',
                                    data: [0, 0, 0],
                                    backgroundColor: 'rgba(0, 0, 0, 0.1)',
                                    borderColor: 'rgba(0, 0, 0, 0.3)',
                                    borderWidth: 1,
                                    order: 1
                                },
                                {
                                    label: 'Current',
                                    data: [0, 0, 0],
                                    backgroundColor: [
                                        'rgba(220, 53, 69, 0.8)',
                                        'rgba(255, 193, 7, 0.8)',
                                        'rgba(40, 167, 69, 0.8)'
                                    ],
                                    borderColor: [
                                        'rgba(220, 53, 69, 1)',
                                        'rgba(255, 193, 7, 1)',
                                        'rgba(40, 167, 69, 1)'
                                    ],
                                    borderWidth: 1,
                                    order: 2
                                },
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        filter: function(item) {
                                            return item.text !== 'Target Line';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                } else {
                    // Create pie or doughnut chart
                    taskPriorityChart = new Chart(taskPriorityCtx, {
                        type: chartType,
                        data: {
                            labels: ['High', 'Normal', 'Low'],
                            datasets: [{
                                data: [0, 0, 0],
                                backgroundColor: [
                                    'rgba(220, 53, 69, 0.8)',
                                    'rgba(255, 193, 7, 0.8)',
                                    'rgba(40, 167, 69, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(220, 53, 69, 1)',
                                    'rgba(255, 193, 7, 1)',
                                    'rgba(40, 167, 69, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                }
                            }
                        }
                    });
                }
            });

            // Task Status Chart
            const taskStatusCtx = document.getElementById('taskStatusChart').getContext('2d');
            let taskStatusChart = new Chart(taskStatusCtx, {
                type: 'bar',
                data: {
                    labels: ['All Projects'],
                    datasets: [
                        {
                            label: 'Completed',
                            data: [0],
                            backgroundColor: 'rgba(40, 167, 69, 0.8)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'For Checking',
                            data: [0],
                            backgroundColor: 'rgba(255, 193, 7, 0.8)',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'For Revision',
                            data: [0],
                            backgroundColor: 'rgba(220, 53, 69, 0.8)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Deferred',
                            data: [0],
                            backgroundColor: 'rgba(108, 117, 125, 0.8)',
                            borderColor: 'rgba(108, 117, 125, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Function to update task priority chart and analytics
            function updateTaskPriorityChart(projectId) {
                const data = projectId === 'all' 
                    ? Object.values(projectTaskData).reduce((acc, project) => {
                        Object.entries(project.priorities).forEach(([priority, count]) => {
                            acc[priority] = (acc[priority] || 0) + count;
                        });
                        return acc;
                    }, {})
                    : projectTaskData[projectId].priorities;

                const total = Object.values(data).reduce((sum, count) => sum + count, 0);
                
                // Update chart data
                const chartData = {
                    labels: ['High', 'Normal', 'Low'],
                    datasets: [{
                        label: 'Task Count',
                        data: [
                            data['High'] || 0,
                            data['Normal'] || 0,
                            data['Low'] || 0
                        ],
                        backgroundColor: [
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(40, 167, 69, 0.8)'
                        ],
                        borderColor: [
                            'rgba(220, 53, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(40, 167, 69, 1)'
                        ],
                        borderWidth: 1
                    }]
                };

                // Update chart based on type
                if (taskPriorityChart.config.type === 'bar') {
                    // For bullet chart
                    taskPriorityChart.data.datasets[1].data = [
                        data['High'] || 0,
                        data['Normal'] || 0,
                        data['Low'] || 0
                    ];
                } else {
                    // For pie/doughnut charts
                    taskPriorityChart.data = chartData;
                }
                taskPriorityChart.update();

                // Update analytics
                document.getElementById('highPriorityCount').textContent = data['High'] || 0;
                document.getElementById('normalPriorityCount').textContent = data['Normal'] || 0;
                document.getElementById('lowPriorityCount').textContent = data['Low'] || 0;

                document.getElementById('highPriorityPercent').textContent = 
                    total ? `${((data['High'] || 0) / total * 100).toFixed(1)}%` : '0%';
                document.getElementById('normalPriorityPercent').textContent = 
                    total ? `${((data['Normal'] || 0) / total * 100).toFixed(1)}%` : '0%';
                document.getElementById('lowPriorityPercent').textContent = 
                    total ? `${((data['Low'] || 0) / total * 100).toFixed(1)}%` : '0%';
            }

            // Function to update task status chart and analytics
            function updateTaskStatusChart(projectId) {
                const data = projectId === 'all'
                    ? Object.values(projectTaskData).reduce((acc, project) => {
                        Object.entries(project.statuses).forEach(([status, count]) => {
                            acc[status] = (acc[status] || 0) + count;
                        });
                        return acc;
                    }, {})
                    : projectTaskData[projectId].statuses;

                const total = Object.values(data).reduce((sum, count) => sum + count, 0);

                // Update chart data
                const chartData = {
                    labels: [projectId === 'all' ? 'All Projects' : projectTaskData[projectId].name],
                    datasets: [
                        {
                            label: 'Completed',
                            data: [data['Completed'] || 0],
                            backgroundColor: 'rgba(40, 167, 69, 0.8)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'For Checking',
                            data: [data['For Checking'] || 0],
                            backgroundColor: 'rgba(255, 193, 7, 0.8)',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'For Revision',
                            data: [data['For Revision'] || 0],
                            backgroundColor: 'rgba(220, 53, 69, 0.8)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Deferred',
                            data: [data['Deferred'] || 0],
                            backgroundColor: 'rgba(108, 117, 125, 0.8)',
                            borderColor: 'rgba(108, 117, 125, 1)',
                            borderWidth: 1
                        }
                    ]
                };

                // Update chart
                taskStatusChart.data = chartData;
                taskStatusChart.update();

                // Update analytics
                document.getElementById('completedStatusCount').textContent = data['Completed'] || 0;
                document.getElementById('checkingStatusCount').textContent = data['For Checking'] || 0;
                document.getElementById('revisionStatusCount').textContent = data['For Revision'] || 0;
                document.getElementById('deferredStatusCount').textContent = data['Deferred'] || 0;

                document.getElementById('completedStatusPercent').textContent = 
                    total ? `${((data['Completed'] || 0) / total * 100).toFixed(1)}%` : '0%';
                document.getElementById('checkingStatusPercent').textContent = 
                    total ? `${((data['For Checking'] || 0) / total * 100).toFixed(1)}%` : '0%';
                document.getElementById('revisionStatusPercent').textContent = 
                    total ? `${((data['For Revision'] || 0) / total * 100).toFixed(1)}%` : '0%';
                document.getElementById('deferredStatusPercent').textContent = 
                    total ? `${((data['Deferred'] || 0) / total * 100).toFixed(1)}%` : '0%';
            }

            let currentPriorityProject = 'all';
            // Event listeners for project selection
            document.getElementById('taskPriorityProject').addEventListener('change', function(e) {
                currentPriorityProject = e.target.value;
                updateTaskPriorityChart(currentPriorityProject);
                updateTaskPriorityTrendsChart(currentPriorityProject);
            });

            document.getElementById('taskStatusProject').addEventListener('change', function(e) {
                updateTaskStatusChart(e.target.value);
            });

            // Event listeners for chart type changes
            document.getElementById('taskPriorityChartType').addEventListener('change', function(e) {
                const chartType = e.target.value;
                taskPriorityChart.destroy();
                
                if (chartType === 'bullet') {
                    // Create bullet chart
                    taskPriorityChart = new Chart(taskPriorityCtx, {
                        type: 'bar',
                        data: {
                            labels: ['High', 'Normal', 'Low'],
                            datasets: [
                                {
                                    label: 'Target',
                                    data: [10, 10, 10],
                                    backgroundColor: 'rgba(0, 0, 0, 0.1)',
                                    borderColor: 'rgba(0, 0, 0, 0.3)',
                                    borderWidth: 1,
                                    order: 1
                                },
                                {
                                    label: 'Current',
                                    data: [0, 0, 0],
                                    backgroundColor: [
                                        'rgba(220, 53, 69, 0.8)',
                                        'rgba(255, 193, 7, 0.8)',
                                        'rgba(40, 167, 69, 0.8)'
                                    ],
                                    borderColor: [
                                        'rgba(220, 53, 69, 1)',
                                        'rgba(255, 193, 7, 1)',
                                        'rgba(40, 167, 69, 1)'
                                    ],
                                    borderWidth: 1,
                                    order: 2
                                },
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        filter: function(item) {
                                            return item.text !== 'Target Line';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                } else {
                    // Create pie or doughnut chart
                    taskPriorityChart = new Chart(taskPriorityCtx, {
                        type: chartType,
                        data: {
                            labels: ['High', 'Normal', 'Low'],
                            datasets: [{
                                data: [0, 0, 0],
                                backgroundColor: [
                                    'rgba(220, 53, 69, 0.8)',
                                    'rgba(255, 193, 7, 0.8)',
                                    'rgba(40, 167, 69, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(220, 53, 69, 1)',
                                    'rgba(255, 193, 7, 1)',
                                    'rgba(40, 167, 69, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                }
                            }
                        }
                    });
                }
                updateTaskPriorityChart(currentPriorityProject);
            });

            document.getElementById('taskStatusChartType').addEventListener('change', function(e) {
                const chartType = e.target.value;
                taskStatusChart.destroy();
                
                if (chartType === 'pie') {
                    // Create pie chart
                    taskStatusChart = new Chart(taskStatusCtx, {
                        type: 'pie',
                        data: {
                            labels: ['Completed', 'For Checking', 'For Revision', 'Deferred'],
                            datasets: [{
                                data: [0, 0, 0, 0],
                                backgroundColor: [
                                    'rgba(40, 167, 69, 0.8)',
                                    'rgba(255, 193, 7, 0.8)',
                                    'rgba(220, 53, 69, 0.8)',
                                    'rgba(108, 117, 125, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(40, 167, 69, 1)',
                                    'rgba(255, 193, 7, 1)',
                                    'rgba(220, 53, 69, 1)',
                                    'rgba(108, 117, 125, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                }
                            }
                        }
                    });
                } else if (chartType === 'bar') {
                    // Create bar chart
                    taskStatusChart = new Chart(taskStatusCtx, {
                        type: 'bar',
                        data: {
                            labels: ['All Projects'],
                            datasets: [
                                {
                                    label: 'Completed',
                                    data: [0],
                                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                                    borderColor: 'rgba(40, 167, 69, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'For Checking',
                                    data: [0],
                                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                                    borderColor: 'rgba(255, 193, 7, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'For Revision',
                                    data: [0],
                                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                                    borderColor: 'rgba(220, 53, 69, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Deferred',
                                    data: [0],
                                    backgroundColor: 'rgba(108, 117, 125, 0.8)',
                                    borderColor: 'rgba(108, 117, 125, 1)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                } else {
                    // Create stacked column chart
                    taskStatusChart = new Chart(taskStatusCtx, {
                        type: 'bar',
                        data: taskStatusChart.data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }
                updateTaskStatusChart(document.getElementById('taskStatusProject').value);
            });

            // Initialize charts
            updateTaskPriorityChart('all');
            updateTaskStatusChart('all');

            // Event listeners for project type chart type changes
            document.getElementById('projectTypeChartType').addEventListener('change', function(e) {
                const chartType = e.target.value;
                projectTypeChart.destroy();

                const typeLabels = Object.keys(window.dashboardData.projectTypes);
                const typeValues = Object.values(window.dashboardData.projectTypes);
                const barColors = [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ];
                const borderColors = [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)'
                ];

                if (chartType === 'radar') {
                    // Radar: axes are project types, one dataset for all projects
                    projectTypeChart = new Chart(projectTypeCtx, {
                        type: 'radar',
                        data: {
                            labels: typeLabels,
                            datasets: [{
                                label: 'Projects',
                                data: typeValues,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                }
                            },
                            layout: { padding: 20 },
                            elements: { line: { borderWidth: 3 } },
                            scales: {
                                r: {
                                    beginAtZero: true,
                                    ticks: { display: false }, // Remove numbers
                                    pointLabels: { font: { size: 14 } }
                                }
                            }
                        }
                    });
                } else if (chartType === 'pie') {
                    // Create pie chart
                    projectTypeChart = new Chart(projectTypeCtx, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(window.dashboardData.projectTypes),
                            datasets: [{
                                data: Object.values(window.dashboardData.projectTypes),
                                backgroundColor: [
                                    'rgba(54, 162, 235, 0.8)',
                                    'rgba(255, 99, 132, 0.8)',
                                    'rgba(75, 192, 192, 0.8)',
                                    'rgba(255, 206, 86, 0.8)',
                                    'rgba(153, 102, 255, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(153, 102, 255, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                }
                            }
                        }
                    });
                } else {
                    // Create bar or line chart
                    projectTypeChart = new Chart(projectTypeCtx, {
                        type: chartType,
                        data: {
                            labels: Object.keys(window.dashboardData.projectTypes),
                            datasets: [{
                                label: 'Number of Projects',
                                data: Object.values(window.dashboardData.projectTypes),
                                backgroundColor: [
                                    'rgba(54, 162, 235, 0.8)',
                                    'rgba(255, 99, 132, 0.8)',
                                    'rgba(75, 192, 192, 0.8)',
                                    'rgba(255, 206, 86, 0.8)',
                                    'rgba(153, 102, 255, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(153, 102, 255, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }
            });
        document.getElementById('projectStatusTimeFilter').addEventListener('change', function(e) {
            const filter = e.target.value;
            const projects = window.dashboardData.allProjects || [];
            const filtered = filterProjectsByTime(projects, filter);

            // Count statuses
            let inProgress = 0, completed = 0, deferred = 0;
            filtered.forEach(p => {
                if (p.status === 'In Progress') inProgress++;
                if (p.status === 'Completed') completed++;
                if (p.status === 'Deferred') deferred++;
            });

            // Update chart data for both pie and bar
            if (projectStatusChart && projectStatusChart.data && projectStatusChart.data.datasets) {
                projectStatusChart.data.datasets[0].data = [inProgress, completed, deferred];
                projectStatusChart.update();
            }
        });
        document.getElementById('projectTypeTimeFilter').addEventListener('change', function(e) {
            const filter = e.target.value;
            const projects = window.dashboardData.allProjects || [];
            const filtered = filterProjectsByTime(projects, filter);

            // Count project types
            const typeCounts = {};
            filtered.forEach(p => {
                if (!typeCounts[p.proj_type]) typeCounts[p.proj_type] = 0;
                typeCounts[p.proj_type]++;
            });

            // Prepare data for chart
            const typeLabels = Object.keys(typeCounts);
            const typeValues = Object.values(typeCounts);

            // Update chart data
            if (projectTypeChart && projectTypeChart.data && projectTypeChart.data.datasets) {
                projectTypeChart.data.labels = typeLabels;
                projectTypeChart.data.datasets[0].data = typeValues;
                projectTypeChart.update();
            }
        });

        function formatWeekLabel(period) {
            // period is like "202523"
            if (!period || period.length < 6) return period;
            const year = period.slice(0, 4);
            const week = period.slice(4);
            return `Week ${parseInt(week)}, ${year}`;
        }

        // Historical Task Priority Trends Chart
        const priorityHistoryData = window.dashboardData.priorityHistory || {};
        const trendPeriods = Object.keys(priorityHistoryData);
        const formattedTrendPeriods = trendPeriods.map(formatWeekLabel);
        const priorities = ['High', 'Normal', 'Low'];
        const taskPriorityHistoryCtx = document.getElementById('taskPriorityHistoryChart').getContext('2d');
        let taskPriorityHistoryChart = new Chart(taskPriorityHistoryCtx, {
            type: 'line',
            data: {
                labels: formattedTrendPeriods,
                datasets: priorities.map(priority => ({
                    label: priority,
                    data: trendPeriods.map(period => {
                        const weekData = priorityHistoryData[period] || [];
                        const found = weekData.find(item => item.priority === priority);
                        return found ? found.count : 0;
                    }),
                    backgroundColor: priority === 'High' ? '#e41a1c' : (priority === 'Normal' ? '#ff7f00' : '#4daf4a'),
                    borderColor: priority === 'High' ? '#e41a1c' : (priority === 'Normal' ? '#ff7f00' : '#4daf4a'),
                    fill: false,
                    tension: 0.3
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // Historical Task Status Trends Chart
        const statusHistoryData = window.dashboardData.statusHistory || {};
        const statusTrendPeriods = Object.keys(statusHistoryData);
        const formattedStatusTrendPeriods = statusTrendPeriods.map(formatWeekLabel);
        const statuses = ['Completed', 'For Checking', 'For Revision', 'Deferred'];
        const statusColors = ['#4daf4a', '#ffb300', '#e41a1c', '#888888'];
        const taskStatusHistoryCtx = document.getElementById('taskStatusHistoryChart').getContext('2d');
        let taskStatusHistoryChart = new Chart(taskStatusHistoryCtx, {
            type: 'line',
            data: {
                labels: formattedStatusTrendPeriods,
                datasets: statuses.map((status, i) => ({
                    label: status,
                    data: statusTrendPeriods.map(period => {
                        const weekData = statusHistoryData[period] || [];
                        const found = weekData.find(item => item.status === status);
                        return found ? found.count : 0;
                    }),
                    backgroundColor: statusColors[i],
                    borderColor: statusColors[i],
                    fill: false,
                    tension: 0.3
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        function updateTaskPriorityTrendsChart(projectId) {
        const periods = Object.keys(priorityHistoryData);
        const formattedPeriods = periods.map(formatWeekLabel);
        const datasets = priorities.map(priority => ({
            label: priority,
            data: periods.map(period => {
                let weekData = priorityHistoryData[period] || [];
                if (projectId !== 'all') {
                    weekData = weekData.filter(item => String(item.project_id) === projectId);
                }
                const found = weekData.find(item => item.priority === priority);
                return found ? found.count : 0;
            }),
            backgroundColor: priority === 'High' ? '#e41a1c' : (priority === 'Normal' ? '#ff7f00' : '#4daf4a'),
            borderColor: priority === 'High' ? '#e41a1c' : (priority === 'Normal' ? '#ff7f00' : '#4daf4a'),
            fill: false,
            tension: 0.3
        }));
        taskPriorityHistoryChart.data.labels = formattedPeriods;
        taskPriorityHistoryChart.data.datasets = datasets;
        taskPriorityHistoryChart.update();
    }

    function updateTaskStatusTrendsChart(projectId) {
        const periods = Object.keys(statusHistoryData);
        const formattedPeriods = periods.map(formatWeekLabel);
        const datasets = statuses.map((status, i) => ({
            label: status,
            data: periods.map(period => {
                let weekData = statusHistoryData[period] || [];
                if (projectId !== 'all') {
                    weekData = weekData.filter(item => String(item.project_id) === projectId);
                }
                const found = weekData.find(item => item.status === status);
                return found ? found.count : 0;
            }),
            backgroundColor: statusColors[i],
            borderColor: statusColors[i],
            fill: false,
            tension: 0.3
        }));
        taskStatusHistoryChart.data.labels = formattedPeriods;
        taskStatusHistoryChart.data.datasets = datasets;
        taskStatusHistoryChart.update();
    }

    // Initialize trend charts to match current filter on page load
    updateTaskPriorityTrendsChart('all');
    updateTaskStatusTrendsChart('all');


        const userAssignmentData = window.dashboardData.userAssignment || [];
        const userLabels = userAssignmentData.map(u => u.user);
        const userCounts = userAssignmentData.map(u => u.count);
        const userTotal = userCounts.reduce((a, b) => a + b, 0);

        // Format labels to include percentage
        const userLabelsWithPercent = userLabels.map((label, i) => {
            const percent = userTotal ? ((userCounts[i] / userTotal) * 100).toFixed(1) : 0;
            return `${label} (${percent}%)`;
        });
        const userAssignmentCtx = document.getElementById('userAssignmentChart').getContext('2d');
        new Chart(userAssignmentCtx, {
            type: 'pie',
            data: {
                labels: userLabelsWithPercent,
                datasets: [{
                    label: 'Tasks Assigned',
                    data: userCounts,
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 193, 7, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            color: '#a3a3a3',
                            font: {
                                size: 13,
                                weight: '400'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label.split(' (')[0]}: ${context.parsed}`;
                            }
                        }
                    }
                }
            }
        });
});

function filterProjectsByTime(projects, filter) {
    const now = new Date();
    return projects.filter(project => {
        const created = new Date(project.created_at);
        if (filter === 'week') {
            const weekAgo = new Date(now);
            weekAgo.setDate(now.getDate() - 7);
            return created >= weekAgo;
        }
        if (filter === 'month') {
            return created.getMonth() === now.getMonth() && created.getFullYear() === now.getFullYear();
        }
        if (filter === 'year') {
            return created.getFullYear() === now.getFullYear();
        }
        return true; // 'all'
    });
}



