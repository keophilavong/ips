# Deployment Setup Instructions

## 🔒 Step 1: Secure Database Configuration

Your `backend/db.php` file contains sensitive credentials. Before deploying:

1. **Create production database configuration:**
   ```bash
   cp backend/db.example.php backend/db.php
   ```

2. **Update `backend/db.php` with production credentials:**
   ```php
   $host = "your-production-db-host";
   $user = "your-production-db-user";
   $pass = "your-production-db-password";
   $dbname = "your-production-db-name";
   ```

3. **The `.gitignore` file will prevent committing sensitive data**

## 📦 Step 2: Prepare Files for Deployment

### Files to Upload:
- ✅ All HTML files
- ✅ `assets/` folder (CSS, JS, images)
- ✅ `backend/` folder (PHP files)
- ✅ `components/` folder
- ✅ `uploads/` folder (create empty if needed)
- ✅ `files/` folder (create with write permissions)
- ✅ `.htaccess` file
- ✅ `database_setup_postgresql.sql` (for database setup)
- ✅ `database_activities.sql` (for activities table)

### Files NOT to Upload:
- ❌ `backend/db.php` (create on server with production credentials)
- ❌ `files/*` (user uploads - will be created automatically)
- ❌ Test files (already removed)

## 🗄️ Step 3: Database Setup

1. **Create PostgreSQL database on your hosting**
2. **Run the SQL scripts:**
   ```sql
   -- First run:
   database_setup_postgresql.sql
   
   -- Then run:
   database_activities.sql
   ```

3. **Create admin account:**
   - Default admin: `admin` / `admin123` (change immediately!)

## 📁 Step 4: File Permissions

Set proper permissions on your server:
```bash
# Directories
chmod 755 assets/
chmod 755 backend/
chmod 755 components/
chmod 755 uploads/
chmod 777 files/          # Writable for uploads
chmod 777 uploads/activities/  # Writable for activity images

# Files
chmod 644 *.html
chmod 644 *.php
chmod 644 .htaccess
```

## 🌐 Step 5: Server Configuration

### Apache Requirements:
- PHP 7.4 or higher
- PostgreSQL PDO extension enabled
- mod_rewrite enabled
- File uploads enabled

### PHP Extensions Required:
- `pdo`
- `pdo_pgsql`
- `pgsql`

## ✅ Step 6: Verify Deployment

After deployment, test:
1. ✅ Homepage loads
2. ✅ Database connection works
3. ✅ File uploads work
4. ✅ Login works
5. ✅ All pages load correctly
6. ✅ Mobile responsive design

## 🚀 Quick Deploy Options

### Option A: Heroku (Recommended)
1. Install Heroku CLI
2. Login: `heroku login`
3. Create app: `heroku create your-app-name`
4. Add PostgreSQL: `heroku addons:create heroku-postgresql:hobby-dev`
5. Deploy: `git push heroku main`
6. Set config vars in Heroku dashboard

### Option B: Traditional Hosting
1. Upload files via FTP/cPanel
2. Create database in hosting panel
3. Update `backend/db.php` with production credentials
4. Run SQL scripts
5. Set file permissions

### Option C: ngrok (Quick Testing)
See `DEPLOY_NGROK.md` for instructions

## 🔐 Security Checklist

- [ ] Changed default admin password
- [ ] Updated database credentials
- [ ] Set proper file permissions
- [ ] Removed test files
- [ ] `.htaccess` is protecting sensitive files
- [ ] Database credentials are not in version control

## 📞 Need Help?

If you encounter issues:
1. Check server error logs
2. Verify PHP extensions are enabled
3. Test database connection separately
4. Check file permissions

