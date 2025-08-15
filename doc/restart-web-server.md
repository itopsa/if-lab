# How to Restart Web Server on Ubuntu

## Apache Web Server (Most Common)

### Check Apache Status
```bash
sudo systemctl status apache2
```

### Restart Apache
```bash
# Restart Apache completely
sudo systemctl restart apache2

# Or reload configuration (faster, keeps connections)
sudo systemctl reload apache2

# Or stop and start
sudo systemctl stop apache2
sudo systemctl start apache2
```

### Enable Apache to Start on Boot
```bash
sudo systemctl enable apache2
```

## Nginx Web Server

### Check Nginx Status
```bash
sudo systemctl status nginx
```

### Restart Nginx
```bash
# Restart Nginx completely
sudo systemctl restart nginx

# Or reload configuration (faster)
sudo systemctl reload nginx

# Or stop and start
sudo systemctl stop nginx
sudo systemctl start nginx
```

### Enable Nginx to Start on Boot
```bash
sudo systemctl enable nginx
```

## PHP-FPM (if using Nginx)

### Restart PHP-FPM
```bash
# Restart PHP-FPM
sudo systemctl restart php8.1-fpm

# Or reload
sudo systemctl reload php8.1-fpm
```

## XAMPP/LAMPP

### Restart XAMPP
```bash
# Stop XAMPP
sudo /opt/lampp/lampp stop

# Start XAMPP
sudo /opt/lampp/lampp start

# Or restart all services
sudo /opt/lampp/lampp restart
```

## PHP Development Server

### If using PHP built-in server
```bash
# Stop the server (Ctrl+C in the terminal where it's running)
# Then restart
php -S 0.0.0.0:8080
```

## Troubleshooting

### Check if Services are Running
```bash
# Check all web-related services
sudo systemctl status apache2 nginx mysql

# Check what's listening on port 80
sudo netstat -tlnp | grep :80

# Check what's listening on port 443
sudo netstat -tlnp | grep :443
```

### Check Error Logs
```bash
# Apache error logs
sudo tail -f /var/log/apache2/error.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log

# PHP error logs
sudo tail -f /var/log/php8.1-fpm.log
```

### Test Configuration
```bash
# Test Apache configuration
sudo apache2ctl configtest

# Test Nginx configuration
sudo nginx -t
```

## Common Issues and Solutions

### Port Already in Use
```bash
# Check what's using port 80
sudo lsof -i :80

# Kill the process if needed
sudo kill -9 PROCESS_ID
```

### Permission Issues
```bash
# Fix file permissions
sudo chown -R www-data:www-data /var/www/html/bowling-db/
sudo chmod -R 755 /var/www/html/bowling-db/
```

### Configuration Errors
```bash
# Check Apache syntax
sudo apache2ctl configtest

# Check Nginx syntax
sudo nginx -t
```

## Quick Commands for Your Bowling Database

### Restart Everything
```bash
# Restart Apache
sudo systemctl restart apache2

# Restart MySQL
sudo systemctl restart mysql

# Check status
sudo systemctl status apache2 mysql
```

### Test Your Application
```bash
# Test locally
curl -I http://localhost/bowling-db/

# Test from external IP
curl -I http://85.9.197.228/bowling-db/
```

## After Restarting

1. **Test your application**: `http://85.9.197.228/bowling-db/`
2. **Check for errors** in the browser console
3. **Verify database connection** is working
4. **Test all pages** (Dashboard, Bowlers, Series, etc.)

## Automatic Restart on Boot

### Enable Services
```bash
# Enable Apache to start on boot
sudo systemctl enable apache2

# Enable MySQL to start on boot
sudo systemctl enable mysql

# Enable Nginx to start on boot (if using)
sudo systemctl enable nginx
```

### Check Enabled Services
```bash
# List all enabled services
sudo systemctl list-unit-files --state=enabled | grep -E "(apache|nginx|mysql)"
```
