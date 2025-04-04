<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Details</title>
</head>
<body>
  <!-- Project Overview Section -->
<div id="projectOverviewSection" class="bg-white p-6 rounded-lg shadow-lg">
  <section class="project-overview">
      <!-- Project Header -->
      <div class="project-header border-b border-gray-200 pb-4 mb-6">
          <h1 id="projectName" class="text-2xl font-bold text-green-600 editable" contenteditable="true">Project Name</h1>
          <p id="projectDescription" class="text-gray-700 mt-2 editable" contenteditable="true">
              Project description goes here. This is a brief overview of the project objectives and goals.
          </p>
      </div>

      <!-- Task Table Wrapper -->
      <div class="task-table-wrapper">
          <div class="button-table-wrapper flex justify-start space-x-4">
              <!-- Buttons -->
              <button id="mainTableBtn" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
                  Main Table
              </button>
              <button id="calendarBtn" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
                  Calendar
              </button>
          </div>
      </div>
  </section>

  <!-- Main Table Section -->
  <section class="group-section mt-8">
      <div class="group-container bg-gray-50 p-4 rounded-lg shadow">
          <!-- Placeholder for group content -->
          <p class="text-gray-500">Noooo ayaw mag refresh groups available. Add a new group to get started.</p>
      </div>
      <div class="add-group-container mt-4">
          <button id="addGroupBtn" class="bg-blue-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
              Add New Group
          </button>
      </div>
  </section>

  <!-- Calendar Section -->
  <section class="calendar-section mt-8">
      <div class="calendar-header flex items-center justify-between bg-gray-100 p-4 rounded-lg shadow">
          <button id="prevMonth" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
              Prev
          </button>
          <span id="monthYearDisplay" class="text-gray-700 font-semibold">Month Year</span>
          <button id="nextMonth" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
              Next
          </button>
      </div>
      <div class="calendar-grid mt-4 bg-gray-50 p-4 rounded-lg shadow">
          <!-- Placeholder for calendar grid -->
          <p class="text-gray-500">Calendar content will appear here.</p>
      </div>
  </section>

  <!-- Footer -->
  <footer class="footer mt-8 border-t border-gray-200 pt-4">
      <p class="text-center text-gray-500">&copy; 2024 City Engineering Office. All rights reserved.</p>
  </footer>
</div>

  <script>
    // Function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Set project name and description from URL parameters
    document.addEventListener('DOMContentLoaded', () => {
        const projectName = getUrlParameter('projectName');
        const projectDescription = getUrlParameter('projectDescription');

        if (projectName) {
            document.getElementById('projectName').textContent = projectName;
        }
        if (projectDescription) {
            document.getElementById('projectDescription').textContent = projectDescription;
        }
    });
  </script>
</body>
</html>
