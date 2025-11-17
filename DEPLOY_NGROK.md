# Quick Demo Setup with ngrok

This is the fastest way to share your website with customers for testing.

## Step 1: Download ngrok

1. Go to https://ngrok.com/download
2. Download ngrok for Windows
3. Extract the `ngrok.exe` file to a folder (e.g., `C:\ngrok\`)

## Step 2: Start XAMPP

1. Open XAMPP Control Panel
2. Start **Apache** (make sure it's running on port 80)
3. Make sure your website is accessible at `http://localhost/internal-education-worker-report/`

## Step 3: Run ngrok

1. Open Command Prompt or PowerShell
2. Navigate to where you extracted ngrok:
   ```cmd
   cd C:\ngrok
   ```
3. Run ngrok:
   ```cmd
   ngrok http 80
   ```
   Or if Apache is on a different port:
   ```cmd
   ngrok http 8080
   ```

## Step 4: Get Your Public URL

After running ngrok, you'll see something like:
```
Forwarding   https://abc123.ngrok.io -> http://localhost:80
```

**Share this URL with your customers!**

Example: `https://abc123.ngrok.io/internal-education-worker-report/`

## Step 5: Update Database Connection (if needed)

If your database is on a remote server (like `192.168.100.17`), make sure:
- The database server allows connections from the internet
- Or use a cloud database service

## Important Notes:

⚠️ **ngrok is FREE but:**
- URLs change each time you restart ngrok (unless you have a paid account)
- Only works when your computer and XAMPP are running
- Good for demos, not for permanent hosting

✅ **For permanent hosting, use one of the options in DEPLOYMENT_GUIDE.md**

## Alternative: LocalTunnel (No Download Needed)

If you have Node.js installed:

```cmd
npm install -g localtunnel
lt --port 80
```

This will give you a public URL similar to ngrok.

