{{-- <x-app-layout> --}}
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <link rel="stylesheet" href="{{ asset('frontend/css/dashboard_styles.css') }}">
    </head>
    <body>
    
        {{-- <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </x-slot> --}}
    
        <div class="header">
            <a href="/public/Dashboard.html" class="logo">
                <img src="{{ asset('frontend/img/341835580_3377258052532133_4186880548703356922_n.jpg') }}" alt="CEO">
            </a>
            <div class="header-right">
                <a href="Dashboard.html">Dashboard</a>
                <a class="active" href="#projects">Projects</a>
                <a href="#archived" id="archiveLink">Archive</a>
                <a href="Profile.html" class="logo">
                    <img src="{{ asset('img/prof_pic.svg') }}" alt="Profile">
                </a>
            </div>
        </div>
    
        <div class="container">
            <h1 id="noProjectsText">No Projects available</h1>
            <div id="projectContainer" class="project-container"></div>
            <button id="newProjectBtn" class="btn">Add Project</button>
        </div>
    
        <div class="navigation-pane" id="navigationPane">
            <h2>Project List</h2>
            <ul id="projectList"></ul>
        </div>
    
        <div id="popupWindow" class="popup">
            <div class="popup-content">
                <span class="close-btn">&times;</span>
                <h2>Create a New Project</h2>
                <form id="projectForm">
                    <label for="project-name">Project Name:</label>
                    <input type="text" id="project-name" name="project-name" required><br><br>
    
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" required><br><br>
    
                    <label for="description">Project Description:</label>
                    <textarea id="description" name="description" rows="4" cols="50" class="fixed-textarea"></textarea><br><br>
                    
                    <button type="submit" class="btn">Create</button>
                </form>
            </div>
        </div>
    
        <div id="editPopupWindow" class="popup">
            <div class="popup-content">
                <span class="close-btn">&times;</span>
                <h2>Edit Project</h2>
                <form id="editProjectForm">
                    <label for="edit-project-name">Project Name:</label>
                    <input type="text" id="edit-project-name" name="edit-project-name" required><br><br>
    
                    <label for="edit-location">Location:</label>
                    <input type="text" id="edit-location" name="edit-location" required><br><br>
    
                    <label for="edit-description">Project Description:</label>
                    <textarea id="edit-description" name="edit-description" rows="4" cols="50" class="fixed-textarea"></textarea><br><br>
    
                    <button type="submit" class="btn">Update</button>
                </form>
            </div>
        </div>
    
        <div id="archivePopupWindow" class="popup">
            <div class="popup-content">
                <span class="close-btn">&times;</span>
                <h2>Archived Projects</h2>
                <div id="archivedProjectsContainer" class="archived-projects-container"></div>
            </div>
        </div>
    
        <script src="{{ asset('frontend/javascript/dashboard_script.js') }}"></script>
    
    {{-- </x-app-layout> --}}
    </body>
    </html>