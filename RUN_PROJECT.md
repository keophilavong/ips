# How to Run the Project

## 🎯 Quick Start (3 Steps)

### 1. Install Docker Desktop
- Download: https://www.docker.com/products/docker-desktop
- Install and **start Docker Desktop**
- Wait until Docker Desktop shows "Docker Desktop is running"

### 2. Open Terminal in Project Folder

**Option A: Using File Explorer**
1. Navigate to: `C:\xampp\htdocs\internal-education-worker-report`
2. Right-click in the folder → "Open in Terminal" or "Open PowerShell window here"

**Option B: Using Command Line**
```powershell
cd C:\xampp\htdocs\internal-education-worker-report
```

### 3. Run Docker Compose
```powershell
docker-compose up -d
```

**First time?** This will:
- Download PHP 8.1 and Apache image (~200MB)
- Build your application container
- Take 2-5 minutes

**Subsequent runs?** Starts in seconds!

### 4. Access the Application
Open your browser and go to:
```
http://localhost:8080
```

🎉 **Your application is now running!**

---

## 📋 Useful Commands

### Check if containers are running:
```powershell
docker-compose ps
```

### View logs (see what's happening):
```powershell
docker-compose logs -f
```
Press `Ctrl+C` to exit logs

### Stop the application:
```powershell
docker-compose down
```

### Restart the application:
```powershell
docker-compose restart
```

### Rebuild after code changes:
```powershell
docker-compose up -d --build
```

---

## 🔧 Troubleshooting

### Problem: "Port 8080 is already in use"
**Solution:** Change the port in `docker-compose.yml`:
```yaml
ports:
  - "8081:80"  # Change 8080 to 8081 or any other port
```
Then access at: `http://localhost:8081`

### Problem: "Cannot connect to Docker daemon"
**Solution:** Make sure Docker Desktop is running. Check system tray for Docker icon.

### Problem: "Database connection failed"
**Solution:** 
1. Check if your database server (183.182.99.33:5555) is accessible
2. Verify database credentials in `docker-compose.yml`
3. Check logs: `docker-compose logs web`

### Problem: "Permission denied" or file upload issues
**Solution:**
```powershell
docker-compose exec web chown -R www-data:www-data /var/www/html/files
docker-compose exec web chown -R www-data:www-data /var/www/html/uploads
```

### Problem: Need to start fresh
**Solution:**
```powershell
docker-compose down
docker-compose up -d --build
```

---

## 📁 Project Structure in Docker

- **Web Server:** PHP 8.1 + Apache
- **Port:** 8080 (maps to container port 80)
- **Database:** External PostgreSQL at 183.182.99.33:5555
- **Files:** Your local `files/` and `uploads/` folders are mounted

---

## 🚀 Alternative: Run Without Docker (XAMPP)

If you prefer to use XAMPP instead:

1. **Start XAMPP** (Apache and PostgreSQL)
2. **Access directly:** `http://localhost/internal-education-worker-report`
3. **Database:** Already configured in `backend/db.php`

---

## ✅ Verification Checklist

After running `docker-compose up -d`, verify:

- [ ] Docker Desktop is running
- [ ] Container is running: `docker-compose ps` shows "Up"
- [ ] Browser shows the application at `http://localhost:8080`
- [ ] No errors in logs: `docker-compose logs web`

---

## 📞 Need Help?

1. Check container status: `docker-compose ps`
2. View logs: `docker-compose logs -f web`
3. Check Docker Desktop dashboard for container status

---

## 🎓 Next Steps

1. ✅ Application is running
2. ✅ Test login functionality
3. ✅ Test file uploads
4. ✅ Verify database connection works

Happy coding! 🚀




