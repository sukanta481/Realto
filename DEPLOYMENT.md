# Auto-Deployment Setup Guide

## Overview
This repository now has auto-deployment configured using GitHub webhooks. When you push code to the `main` branch, it will automatically deploy to your live server.

## Setup Instructions

### 1. Server Setup

#### A. Make the deployment script executable
```bash
chmod +x deploy.sh
```

#### B. Update the deployment script
Edit `deploy.sh` and change the following line to your actual server path:
```bash
REPO_DIR="/path/to/your/realto"  # Update this!
```

For example:
```bash
REPO_DIR="/var/www/html/realto"
```

#### C. Ensure proper ownership
Make sure your web server user (usually `www-data` or `nginx`) has permission to execute git commands:
```bash
# Give web server user git permissions
sudo chown -R www-data:www-data /var/www/html/realto
sudo -u www-data git config --global --add safe.directory /var/www/html/realto
```

### 2. Environment Configuration

Add these variables to your `.env` file on the server:

```env
# GitHub Webhook Settings
GITHUB_WEBHOOK_SECRET=your_random_secret_here
DEPLOY_BRANCH=main
```

Generate a secure secret:
```bash
php -r "echo bin2hex(random_bytes(32));"
```

### 3. GitHub Webhook Configuration

1. Go to your GitHub repository
2. Click **Settings** → **Webhooks** → **Add webhook**
3. Configure the webhook:
   - **Payload URL**: `https://yourdomain.com/api/deploy`
   - **Content type**: `application/json`
   - **Secret**: Use the same secret from your `.env` file
   - **Which events**: Select "Just the push event"
   - **Active**: ✓ Checked

4. Click **Add webhook**

### 4. Test the Deployment

#### Option A: Push to GitHub (recommended)
```bash
git add .
git commit -m "Test auto-deployment"
git push origin main
```

Check the webhook delivery in GitHub:
- Go to Settings → Webhooks → Recent Deliveries
- You should see a 200 response

#### Option B: Manual deployment endpoint
Visit: `https://yourdomain.com/api/deploy/manual` (requires authentication)

### 5. Verify Logs

Check deployment logs on your server:
```bash
tail -f storage/logs/deployment.log
```

## How It Works

1. You push code to GitHub (main branch)
2. GitHub sends a webhook to your server
3. Server receives the webhook at `/api/deploy`
4. `DeployController` validates the signature and triggers `deploy.sh`
5. Deployment script:
   - Puts site in maintenance mode
   - Pulls latest code
   - Installs dependencies
   - Runs migrations
   - Builds frontend assets
   - Clears caches
   - Brings site back online

## Troubleshooting

### Issue: Getting 200 but code not updating

**Solution 1: Check deployment logs**
```bash
tail -f storage/logs/deployment.log
```

**Solution 2: Check Laravel logs**
```bash
tail -f storage/logs/laravel.log
```

**Solution 3: Verify script permissions**
```bash
ls -la deploy.sh
# Should show: -rwxr-xr-x (executable)
```

**Solution 4: Test script manually**
```bash
sudo -u www-data bash deploy.sh
```

**Solution 5: Check git configuration**
```bash
cd /var/www/html/realto
sudo -u www-data git pull origin main
# If this fails, deployment won't work
```

### Common Issues

1. **Permission denied**
   - Run: `chmod +x deploy.sh`
   - Ensure web server user can execute git

2. **Script not found**
   - Verify `deploy.sh` exists in project root
   - Check path in `DeployController.php`

3. **Git pull fails**
   - Ensure SSH key is set up for web server user
   - OR use HTTPS with credentials cached
   - Run: `git config credential.helper store`

4. **Composer/NPM not found**
   - Add to PATH in deploy.sh:
     ```bash
     export PATH="$PATH:/usr/local/bin"
     ```

5. **Process runs but doesn't complete**
   - Check PHP execution time limits
   - Check server memory limits

## Security Notes

- Keep your `GITHUB_WEBHOOK_SECRET` secure
- Never commit `.env` file to git
- Ensure `deploy.sh` only pulls from trusted branches
- Consider adding IP whitelist for webhook endpoint
- Review Laravel logs regularly

## Customization

### Deploy different branch
Change in `.env`:
```env
DEPLOY_BRANCH=production
```

### Skip migrations
Edit `deploy.sh` and comment out:
```bash
# php artisan migrate --force
```

### Add custom steps
Add your commands in `deploy.sh` before the `php artisan up` line.

## Support

If deployment fails:
1. Check GitHub webhook delivery status
2. Review `storage/logs/deployment.log`
3. Review `storage/logs/laravel.log`
4. Test deployment script manually
5. Verify all permissions and paths
