# CityWorks

CityWorks is a web-based project and task management system designed for city or municipal organizations. It helps teams manage activities, projects, tasks, and personnel efficiently with dashboards, analytics, and notifications.

## Features

- Project (Activity) management with types and statuses
- Task management with priorities, subtasks, and assignments
- Dashboard with statistics, charts, and export options (CSV/PDF)
- User roles and permissions
- Notifications for key events (project creation, completion, etc.)
- Activity log for auditing
- Calendar integration for events and deadlines
- Archive and restore projects

## Tech Stack

- **Backend:** Laravel (PHP)
- **Frontend:** Blade, Tailwind CSS, JavaScript
- **Database:** MySQL (or compatible)
- **Other:** Spatie Activity Log, jsPDF, Chart.js

## Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/CityWorks.git
   cd CityWorks
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Configure environment:**
   - Copy `.env.example` to `.env` and update database and mail settings.
   - Generate application key:
     ```bash
     php artisan key:generate
     ```

4. **Run migrations and seeders:**
   ```bash
   php artisan migrate --seed
   ```

5. **Start the development server:**
   ```bash
   php artisan serve
   ```

6. **Access the app:**
   - Visit [http://localhost:8000](http://localhost:8000) in your browser.

## Usage

- Log in with your credentials.
- Create and manage projects and tasks.
- Use the dashboard for analytics and exports.
- Manage users and permissions as an admin.

## Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

## License

[MIT](LICENSE)