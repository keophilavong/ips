# Docker Setup Guide

This guide will help you run the Internal Education Worker Report System using Docker.

## Prerequisites

- Docker Desktop installed on your system
  - Download from: https://www.docker.com/products/docker-desktop
- Docker Compose (usually included with Docker Desktop)

## Quick Start

### Option 1: Using Docker Compose (Recommended)

1. **Open terminal/command prompt** in the project directory

2. **Build and start containers:**
   ```bash
   docker-compose up -d
   ```
   The `-d` flag runs containers in detached mode (in the background)

3. **Access the application:**
   - Open your browser and go to: `http://localhost:8080`
   - The application will be running on port 8080

4. **Check container status:**
   ```bash
   docker-compose ps
   ```

5. **View logs:**
   ```bash
   docker-compose logs -f
   ```

6. **Stop containers:**
   ```bash
   docker-compose down
   ```

7. **Stop and remove volumes (clean reset):**
   ```bash
   docker-compose down -v
   ```

### Option 2: Using Docker Commands Directly

1. **Build the Docker image:**
   ```bash
   docker build -t education-worker-app .
   ```

2. **Run the container:**
   ```bash
   docker run -d -p 8080:80 --name education-worker-web education-worker-app
   ```

3. **Access the application:**
   - Open browser: `http://localhost:8080`

## Database Setup

### Using Docker Compose (Automatic)

The `docker-compose.yml` automatically:
- Creates a PostgreSQL database container
- Initializes the database using `database_setup_postgresql.sql`
- Sets up the connection between web and database containers

**Default Database Credentials (in docker-compose.yml):**
- Host: `db` (container name)
- Port: `5432` (internal)
- User: `edu_user`
- Password: `edu_password`
- Database: `edu_pro`

### Using External Database

If you want to use an external PostgreSQL database instead:

1. **Edit `docker-compose.yml`** and remove or comment out the `db` service

2. **Update environment variables** in the `web` service:
   ```yaml
   environment:
     - DB_HOST=your-external-db-host
     - DB_PORT=5432
     - DB_USER=your-username
     - DB_PASS=your-password
     - DB_NAME=your-database-name
   ```

3. **Or create a `.env` file** in the project root:
   ```
   DB_HOST=your-external-db-host
   DB_PORT=5432
   DB_USER=your-username
   DB_PASS=your-password
   DB_NAME=your-database-name
   ```

4. **Update `docker-compose.yml`** to use `.env`:
   ```yaml
   environment:
     - DB_HOST=${DB_HOST}
     - DB_PORT=${DB_PORT}
     - DB_USER=${DB_USER}
     - DB_PASS=${DB_PASS}
     - DB_NAME=${DB_NAME}
   ```

## File Uploads

The `files/` and `uploads/` directories are mounted as volumes, so uploaded files will persist even if containers are restarted.

## Common Commands

### View running containers:
```bash
docker-compose ps
```

### View logs:
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f web
docker-compose logs -f db
```

### Restart services:
```bash
docker-compose restart
```

### Rebuild after code changes:
```bash
docker-compose up -d --build
```

### Access container shell:
```bash
# Web container
docker-compose exec web bash

# Database container
docker-compose exec db psql -U edu_user -d edu_pro
```

### Stop all containers:
```bash
docker-compose down
```

### Stop and remove everything (including volumes):
```bash
docker-compose down -v
```

## Troubleshooting

### Port Already in Use

If port 8080 is already in use, edit `docker-compose.yml` and change:
```yaml
ports:
  - "8080:80"  # Change 8080 to another port like 8081, 3000, etc.
```

### Database Connection Issues

1. **Check if database container is running:**
   ```bash
   docker-compose ps
   ```

2. **Check database logs:**
   ```bash
   docker-compose logs db
   ```

3. **Verify environment variables:**
   ```bash
   docker-compose exec web env | grep DB_
   ```

### Permission Issues

If you encounter permission issues with file uploads:

```bash
docker-compose exec web chown -R www-data:www-data /var/www/html/files
docker-compose exec web chown -R www-data:www-data /var/www/html/uploads
```

### Clear Everything and Start Fresh

```bash
# Stop and remove containers, networks, and volumes
docker-compose down -v

# Remove the image
docker rmi internal-education-worker-report-web

# Rebuild from scratch
docker-compose up -d --build
```

## Production Deployment

For production deployment:

1. **Update database credentials** in `docker-compose.yml` or use environment variables
2. **Use a reverse proxy** (nginx) in front of the application
3. **Set up SSL/TLS** certificates
4. **Configure proper backup** for the database volume
5. **Use Docker secrets** or environment variables for sensitive data
6. **Set resource limits** in docker-compose.yml:
   ```yaml
   services:
     web:
       deploy:
         resources:
           limits:
             cpus: '1'
             memory: 512M
   ```

## Windows-Specific Notes

If you're on Windows:
- Use PowerShell or Command Prompt
- Make sure Docker Desktop is running
- File paths in volumes work automatically with Docker Desktop

## Next Steps

1. Access the application at `http://localhost:8080`
2. Import your database schema if using a new database
3. Test file uploads functionality
4. Configure your production database credentials

## Need Help?

- Check Docker logs: `docker-compose logs -f`
- Verify containers are running: `docker-compose ps`
- Check Docker Desktop dashboard for container status

