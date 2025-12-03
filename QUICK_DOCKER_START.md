# Quick Docker Start Guide

## 🚀 Fastest Way to Run

### Step 1: Install Docker Desktop
- Download and install Docker Desktop from: https://www.docker.com/products/docker-desktop
- Make sure Docker Desktop is **running** (you'll see the Docker icon in your system tray)

### Step 2: Open Terminal in Project Folder
- Open PowerShell or Command Prompt
- Navigate to your project folder:
  ```powershell
  cd C:\xampp\htdocs\internal-education-worker-report
  ```

### Step 3: Build and Start the Container
```powershell
docker-compose up -d
```
*(The `-d` flag runs it in the background)*

**First time?** This will take a few minutes to download and build the image.

### Step 4: Open Your Browser
Go to: **http://localhost:8080**

### Step 5: Stop When Done
```powershell
docker-compose down
```

## 📝 What Happens

- ✅ Builds PHP 8.1 + Apache container
- ✅ Connects to your existing database (183.182.99.33:5555)
- ✅ Application runs on port 8080
- ✅ File uploads are saved to `files/` and `uploads/` folders

## 🔧 Common Issues

**Port 8080 busy?** Edit `docker-compose.yml` line 8, change `8080` to another port.

**Database not connecting?** Check logs: `docker-compose logs db`

**Need to rebuild?** Run: `docker-compose up -d --build`

## 📚 Full Documentation

See `DOCKER_SETUP.md` for detailed instructions.

