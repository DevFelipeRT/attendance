# Attendance & Mentorship MVP

A browser-only Laravel MVP for education teams to manage attendance and mentorship workflows. The system is intended for **admin staff** and **teachers/mentors** who need to organize class groups, track lessons, and record attendance while also managing 1:1 mentorships billed by lesson credits. It is intentionally scoped to classroom operations and mentorship billing only; **monthly class-group billing is out of scope** and not handled by the system.

## Core Use Cases

- Maintain master data: students, teachers/mentors, and subjects.
- Create class groups, enroll students, generate lessons, and take attendance (class groups can operate as regular cohorts or single-student groups).
- Manage mentorships with single-student enrollment, schedule sessions, capture attendance, and record credit-based payments.

## Tech Stack

- **Backend:** PHP 8.2, Laravel 12, Eloquent ORM
- **Frontend tooling:** Vite, Tailwind CSS, Alpine.js
- **Testing(planned, not implemented yet):** PHPUnit

## Quickstart

1. Install PHP dependencies:
   ```bash
   composer install
   ```
2. Create an environment file and generate an app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Set up the database (default is SQLite) and run migrations:
   ```bash
   php artisan migrate
   ```
4. Install frontend dependencies and build assets:
   ```bash
   npm install
   npm run build
   ```
5. Start the local dev environment:
   ```bash
   composer run dev
   ```

## Documentation

- [Domain overview](docs/overview.md)
- [Architecture](docs/architecture.md)
- [Local setup](docs/how-to-local-setup.md)
- [Common tasks](docs/how-to-common-tasks.md)
- [Troubleshooting](docs/troubleshooting.md)

## Scope Notes

- The UI is the only interface; there is no public HTTP API.
- Mentorships are 1:1 (single-student) and billed using lesson/session credits.
- Class groups may be configured for single-student delivery but do not handle monthly billing.
