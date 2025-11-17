# Deployment Guide - Internal Education Worker Report System

This guide provides multiple options to deploy your website so customers can access and test it online.

## 🚀 Quick Deployment Options

### Option 1: Free Hosting with PostgreSQL Support (Recommended for Testing)

#### A. **000webhost.com** (Free)
- ✅ Free PHP hosting
- ✅ PostgreSQL support
- ✅ File uploads allowed
- ❌ Limited bandwidth
- **Steps:**
  1. Sign up at https://www.000webhost.com
  2. Create a new website
  3. Upload all files via File Manager or FTP
  4. Create PostgreSQL database in hosting panel
  5. Update `backend/db.php` with new database credentials
  6. Import your database schema

#### B. **InfinityFree** (Free)
- ✅ Free PHP hosting
- ✅ MySQL/PostgreSQL support
- ✅ Unlimited bandwidth
- **Steps:**
  1. Sign up at https://www.infinityfree.net
  2. Create account and website
  3. Upload files via File Manager
  4. Set up database in control panel

### Option 2: Cloud Platforms (Better Performance)

#### A. **Heroku** (Free tier available)
- ✅ PostgreSQL add-on available
- ✅ Easy deployment
- ✅ Professional hosting
- **Steps:**
  1. Create account at https://www.heroku.com
  2. Install Heroku CLI
  3. Create `Procfile` with: `web: vendor/bin/heroku-php-apache2 .`
  4. Deploy: `git push heroku main`
  5. Add PostgreSQL add-on

#### B. **DigitalOcean** (Paid, ~$5/month)
- ✅ Full control
- ✅ PostgreSQL support
- ✅ Good performance
- **Steps:**
  1. Create account at https://www.digitalocean.com
  2. Create Droplet (Ubuntu + LAMP)
  3. Install PostgreSQL
  4. Upload files via SFTP
  5. Configure Apache

#### C. **AWS Lightsail** (Paid, ~$3.50/month)
- ✅ Easy setup
- ✅ PostgreSQL support
- ✅ Scalable
- **Steps:**
  1. Create AWS account
  2. Launch Lightsail instance
  3. Install LAMP stack
  4. Deploy application

### Option 3: Traditional Web Hosting

#### A. **Hostinger** (Paid, ~$2-3/month)
- ✅ PostgreSQL support
- ✅ Good performance
- ✅ 24/7 support
- **Steps:**
  1. Purchase hosting plan
  2. Upload files via cPanel
  3. Create PostgreSQL database
  4. Update database config

#### B. **Bluehost** (Paid, ~$3/month)
- ✅ PostgreSQL support
- ✅ Easy cPanel interface
- ✅ Reliable hosting

### Option 4: Temporary Demo (Quick Testing)

#### A. **ngrok** (Free for testing)
- ✅ Expose localhost to internet
- ✅ Quick setup
- ✅ Good for demos
- **Steps:**
  1. Download ngrok from https://ngrok.com
  2. Run: `ngrok http 80`
  3. Share the ngrok URL with customers
  - **Note:** Only works when your XAMPP is running

#### B. **LocalTunnel** (Free)
- ✅ Similar to ngrok
- ✅ No signup required
- **Steps:**
  1. Install: `npm install -g localtunnel`
  2. Run: `lt --port 80`
  3. Share the generated URL

## 📋 Pre-Deployment Checklist

Before deploying, make sure to:

### 1. Update Database Configuration
Create a new `backend/db.php` file for production:

```php
<?php
// Production Database Configuration
$host = "your-database-host";
$port = "5432";
$user = "your-database-user"; 
$pass = "your-secure-password";
$dbname = "your-database-name";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
```

### 2. Secure Sensitive Files
- ✅ Remove or secure `backend/db.php` (don't expose credentials)
- ✅ Set proper file permissions (644 for files, 755 for directories)
- ✅ Remove test files (`test-connection.php`, `test-server.php`, etc.)

### 3. Update File Paths
- Check all relative paths work correctly
- Update `assets/js/main.js` if needed for base path detection
- Ensure `files/` directory has write permissions for uploads

### 4. Database Migration
- Export your current database schema
- Import to production database
- Run: `database_setup_postgresql.sql` on production server

### 5. Environment Configuration
- Update `.htaccess` if needed
- Configure PHP settings (upload limits, etc.)
- Set timezone in PHP if needed

## 🔒 Security Considerations

1. **Change Default Passwords**
   - Update admin password
   - Use strong database passwords

2. **File Permissions**
   - `files/` directory: 755 (readable, writable)
   - PHP files: 644
   - `.htaccess`: 644

3. **Hide Sensitive Files**
   - Add to `.htaccess`:
   ```apache
   <FilesMatch "^(db\.php|\.env)$">
       Order allow,deny
       Deny from all
   </FilesMatch>
   ```

## 📦 Files to Upload

Upload these directories and files:
```
/
├── assets/
├── backend/
├── components/
├── uploads/
├── files/ (create if doesn't exist)
├── .htaccess
├── index.html
├── *.html (all HTML files)
└── *.php (all PHP files)
```

## 🧪 Testing After Deployment

1. ✅ Test homepage loads
2. ✅ Test database connection
3. ✅ Test file uploads
4. ✅ Test login functionality
5. ✅ Test all forms
6. ✅ Check mobile responsiveness

## 💡 Recommended for Your Project

**For Quick Demo/Testing:**
- Use **ngrok** or **LocalTunnel** to share your localhost

**For Production/Client Access:**
- Use **Hostinger** or **DigitalOcean** for reliable hosting
- Or **Heroku** if you want easy deployment

## 📞 Need Help?

If you need help with any deployment step, let me know which option you'd like to use and I can provide detailed instructions!

