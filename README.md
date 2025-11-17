# Internal Education Worker Report Tracking System

A web-based system for tracking internal education worker activities, documents, and movement information across teacher colleges, districts, and provinces.

## Prerequisites

- XAMPP (or any PHP server)
- PHP 7.4 or higher
- PostgreSQL 12 or higher
- Web browser

## Setup Instructions

### Step 1: Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache** server
3. Start **MySQL** server

### Step 2: Create Database

1. Open pgAdmin or PostgreSQL command line
2. Run the SQL script `database_setup_postgresql.sql`
3. This will create:
   - Database: `edu-pro`
   - Tables: `users`, `reports`, `admins`, `activities`
   - Default admin account (username: `admin`, password: `admin123`)

### Step 3: Verify Database Configuration

Check `backend/db.php` to ensure database credentials match your setup:
- Host: Your PostgreSQL host (e.g., `localhost` or remote server)
- Port: `5432` (default PostgreSQL port)
- User: Your PostgreSQL username
- Password: Your PostgreSQL password
- Database: `edu-pro`

Update the credentials in `backend/db.php` to match your PostgreSQL setup.

### Step 4: Create Required Directories

The system needs a `files` directory for uploads. Create it manually or it will be created automatically when you upload a file.

### Step 5: Access the Application

1. Open your web browser
2. Navigate to: `http://localhost/internal-education-worker-report/`
3. You should see the homepage

### Step 6: Create an Account or Login

- **Register**: Go to `http://localhost/internal-education-worker-report/register.html`
- **Login**: Go to `http://localhost/internal-education-worker-report/login.html`
- **Admin Login**: Use username `admin` and password `admin123` (change this in production!)

## Project Structure

```
internal-education-worker-report/
├── assets/
│   ├── css/          # Stylesheets
│   └── js/           # JavaScript files
├── backend/          # PHP backend files
├── components/       # Reusable HTML components
├── uploads/          # Upload form pages
├── files/            # Uploaded files (created automatically)
└── index.html        # Homepage
```

## Default Admin Credentials

- **Username**: admin
- **Password**: admin123

⚠️ **IMPORTANT**: Change the admin password in production!

## Troubleshooting

### Database Connection Error
- Ensure PostgreSQL is running
- Check database credentials in `backend/db.php`
- Verify database exists in PostgreSQL
- Ensure PHP PostgreSQL extensions are enabled (`pdo_pgsql`, `pgsql`)

### File Upload Issues
- Check that `files` directory exists and has write permissions
- Verify PHP upload settings in `php.ini`

### Page Not Found
- Ensure Apache is running
- Check that files are in the correct XAMPP htdocs directory
- Verify the URL path matches your directory name

## Features

- User registration and authentication
- Report upload and management
- Admin dashboard
- Activity tracking
- Document management

## Security Notes

- Change default admin password
- Use prepared statements for SQL queries (recommended)
- Implement proper file upload validation
- Use HTTPS in production

