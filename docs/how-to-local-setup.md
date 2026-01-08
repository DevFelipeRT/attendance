# Local Setup

## Prerequisites

- PHP 8.2+
- Composer
- Node.js + npm
- SQLite (default) or another supported database

## Setup Steps

1. Install PHP dependencies:
   ```bash
   composer install
   ```
2. Create environment configuration:
   ```bash
   cp .env.example .env
   ```
3. Generate the app key:
   ```bash
   php artisan key:generate
   ```
4. Configure the database:
   - Default uses SQLite (`DB_CONNECTION=sqlite`).
   - Ensure `database/database.sqlite` exists if you keep SQLite.
5. Run migrations:
   ```bash
   php artisan migrate
   ```
6. Install frontend dependencies and build assets:
   ```bash
   npm install
   npm run build
   ```
7. Start the dev environment:
   ```bash
   composer run dev
   ```

## Notes

- Queue, cache, and session drivers are configured to use the database by default.
- The app is intended for browser usage only; there is no public API to configure.
