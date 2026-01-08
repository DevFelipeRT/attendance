# Troubleshooting

## Common Issues

### “No application encryption key has been specified.”

Generate a key after copying `.env`:
```bash
php artisan key:generate
```

### SQLite database file missing

Create the file if you are using SQLite:
```bash
touch database/database.sqlite
```

### Migration errors about missing tables (sessions/queue/cache)

Ensure database migrations have run:
```bash
php artisan migrate
```

### Assets not loading

Rebuild frontend assets:
```bash
npm install
npm run build
```

### Background jobs not running

Use the standard dev script to run the queue worker:
```bash
composer run dev
```
