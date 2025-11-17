# 🚀 Quick Deployment Guide

## ⚠️ IMPORTANT: Before You Deploy

Your `backend/db.php` file contains **sensitive database credentials**. 

### Option 1: Remove from Git History (Recommended)
If you already pushed `db.php` to GitHub, remove it:

```bash
# Remove db.php from git tracking (but keep local file)
git rm --cached backend/db.php

# Commit the removal
git commit -m "Remove sensitive db.php from repository"

# Push to GitHub
git push origin main
```

### Option 2: Update Credentials
If you want to keep it in git (NOT recommended for production):
1. Change all database credentials to production values
2. Never use the same credentials in production

## 📝 Next Steps to Deploy

### 1. Add All Files to Git
```bash
# Add all files (db.php will be ignored by .gitignore)
git add .

# Commit
git commit -m "Add complete project files with deployment configuration"

# Push to GitHub
git push origin main
```

### 2. Choose Your Deployment Method

#### A. Heroku (Easiest for PHP + PostgreSQL)
```bash
# Install Heroku CLI first: https://devcenter.heroku.com/articles/heroku-cli

# Login to Heroku
heroku login

# Create app
heroku create your-app-name

# Add PostgreSQL database
heroku addons:create heroku-postgresql:hobby-dev

# Set environment variables (if needed)
heroku config:set DB_HOST=your-host
heroku config:set DB_USER=your-user
heroku config:set DB_PASS=your-password
heroku config:set DB_NAME=your-database

# Deploy
git push heroku main

# Open your app
heroku open
```

#### B. Traditional Web Hosting
1. Download/clone from GitHub
2. Upload via FTP or cPanel File Manager
3. Create PostgreSQL database in hosting panel
4. Create `backend/db.php` on server with production credentials
5. Run SQL scripts to set up database
6. Set file permissions (see DEPLOY_SETUP.md)

#### C. Quick Demo with ngrok
```bash
# Download ngrok: https://ngrok.com/download
# Run:
ngrok http 80

# Share the ngrok URL with customers
```

## ✅ Post-Deployment Checklist

- [ ] Database connection works
- [ ] File uploads work (check `files/` directory permissions)
- [ ] Login works
- [ ] All pages load correctly
- [ ] Mobile responsive design works
- [ ] Admin can manage activities
- [ ] Users can upload documents

## 🔒 Security Reminders

- ✅ `.gitignore` protects `backend/db.php` from being committed
- ✅ `.htaccess` protects sensitive files from web access
- ⚠️ Change default admin password immediately
- ⚠️ Use strong database passwords
- ⚠️ Keep `backend/db.php` out of version control

## 📞 Need Help?

See `DEPLOY_SETUP.md` for detailed instructions.

