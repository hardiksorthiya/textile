# Textile Software - Laravel Setup Instructions

## Project Setup Complete ✅

Your Laravel project has been successfully installed and configured for MySQL database connection.

## Database Configuration

The `.env` file has been configured with the following settings:
- **DB_CONNECTION**: mysql
- **DB_HOST**: 127.0.0.1
- **DB_PORT**: 3306
- **DB_DATABASE**: textile
- **DB_USERNAME**: root
- **DB_PASSWORD**: (empty - default XAMPP setting)

## Next Steps

### 1. Start XAMPP MySQL Service
1. Open XAMPP Control Panel
2. Start the **MySQL** service
3. Wait until it shows "Running" status

### 2. Create the Database
Once MySQL is running, you have two options:

**Option A: Using the provided script**
```bash
php create_database.php
```

**Option B: Using phpMyAdmin**
1. Open http://localhost/phpmyadmin in your browser
2. Click on "New" in the left sidebar
3. Enter database name: `textile`
4. Select collation: `utf8mb4_unicode_ci`
5. Click "Create"

### 3. Run Migrations
After the database is created, run:
```bash
php artisan migrate
```

### 4. Start Development Server
```bash
php artisan serve
```

Then visit: http://localhost:8000

## Testing Database Connection

To test if the database connection is working:
```bash
php artisan migrate:status
```

## Notes

- Make sure XAMPP MySQL is running before running migrations
- If you changed the MySQL root password in XAMPP, update `DB_PASSWORD` in `.env` file
- The database name can be changed in `.env` if needed

