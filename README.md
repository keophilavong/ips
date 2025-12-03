# Internal Education Worker Report Tracking System

A web-based system for tracking internal education worker activities, documents, and movement information across teacher colleges, districts, and provinces.

## Prerequisites

### Option 1: Docker (Recommended for Production)
- Docker Desktop (or Docker Engine + Docker Compose)
- Web browser

### Option 2: Local Development
- XAMPP (or any PHP server)
- PHP 7.4 or higher
- PostgreSQL 12 or higher
- Web browser

## Setup Instructions

## 🐳 Docker Setup (Recommended)

### Quick Start with Docker

1. **Make sure Docker is running**
   - Start Docker Desktop (Windows/Mac) or Docker service (Linux)

2. **Build and start containers**
   ```bash
   docker-compose up -d
   ```
   This will:
   - Build the PHP/Apache web server
   - Start PostgreSQL database
   - Initialize the database with schema
   - Make the app available at `http://localhost:8080`

3. **Access the application**
   - Open browser: `http://localhost:8080`
   - Default admin: username `admin`, password `admin123`

4. **View logs** (optional)
   ```bash
   docker-compose logs -f
   ```

5. **Stop containers**
   ```bash
   docker-compose down
   ```

6. **Stop and remove all data** (clean slate)
   ```bash
   docker-compose down -v
   ```

### Docker Configuration

The `docker-compose.yml` file includes:
- **Web service**: PHP 8.1 with Apache on port 8080
- **Database service**: PostgreSQL 15 on port 5433
- **Environment variables**: Set in `docker-compose.yml` or use `.env` file

### Using External Database

If you want to use an existing PostgreSQL database instead of the Docker one:

1. Update `docker-compose.yml`:
   ```yaml
   web:
     environment:
       - DB_HOST=your-database-host
       - DB_PORT=5432
       - DB_USER=your-username
       - DB_PASS=your-password
       - DB_NAME=your-database
   ```

2. Remove or comment out the `db` service and `depends_on` section

3. Restart: `docker-compose up -d`

### Docker Commands Reference

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Rebuild after changes
docker-compose up -d --build

# View logs
docker-compose logs -f web
docker-compose logs -f db

# Access database directly
docker-compose exec db psql -U edu_user -d edu_pro

# Access web container shell
docker-compose exec web bash

# Restart a specific service
docker-compose restart web
```

---

## Local Development Setup (XAMPP)

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

