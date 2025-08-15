# phpMyAdmin Troubleshooting Guide

## Issue: Seeing Raw PHP Code Instead of Web Interface

When you see raw PHP code like `<?php declare(strict_types=1);`, it means PHP isn't being processed by the web server.

## Solution 1: Check PHP Installation

### Verify PHP is installed:
```bash
# Check PHP version
php --version

# Check if PHP module is loaded in Apache
apache2ctl -M | grep php
```

### Install PHP if missing:
```bash
# Update package list
sudo apt update

# Install PHP and Apache module
sudo apt install php php-mysql php-common php-mbstring php-xml php-curl php-gd php-zip -y

# Restart Apache
sudo systemctl restart apache2
```

## Solution 2: Enable PHP Module in Apache

### Check if PHP module is enabled:
```bash
# List enabled modules
ls /etc/apache2/mods-enabled/ | grep php

# If no PHP modules are enabled, enable them:
sudo a2enmod php8.1
sudo systemctl restart apache2
```

### Alternative: Enable PHP-FPM:
```bash
# Install PHP-FPM
sudo apt install php-fpm -y

# Enable PHP-FPM module
sudo a2enmod proxy_fcgi setenvif
sudo a2enconf php8.1-fpm

# Restart Apache
sudo systemctl restart apache2
```

## Solution 3: Check phpMyAdmin Installation

### Verify phpMyAdmin is properly installed:
```bash
# Check if phpMyAdmin files exist
ls -la /usr/share/phpmyadmin/

# Check Apache configuration
ls -la /etc/apache2/conf-enabled/ | grep phpmyadmin
```

### Reinstall phpMyAdmin if needed:
```bash
# Remove existing installation
sudo apt remove --purge phpmyadmin -y
sudo apt autoremove -y

# Reinstall phpMyAdmin
sudo apt install phpmyadmin -y

# During installation:
# - Choose Apache2 when prompted
# - Select "Yes" for database configuration
# - Enter your MySQL root password
```

## Solution 4: Manual phpMyAdmin Setup

### If automatic installation fails:
```bash
# Download phpMyAdmin
cd /tmp
wget https://www.phpmyadmin.net/downloads/phpMyAdmin-latest-all-languages.tar.gz

# Extract
tar -xzf phpMyAdmin-latest-all-languages.tar.gz

# Move to web directory
sudo mv phpMyAdmin-*/ /usr/share/phpmyadmin

# Set permissions
sudo chown -R www-data:www-data /usr/share/phpmyadmin
sudo chmod -R 755 /usr/share/phpmyadmin

# Create configuration
sudo cp /usr/share/phpmyadmin/config.sample.inc.php /usr/share/phpmyadmin/config.inc.php
```

### Create Apache configuration:
```bash
sudo nano /etc/apache2/conf-available/phpmyadmin.conf
```

Add this content:
```apache
Alias /phpmyadmin /usr/share/phpmyadmin

<Directory /usr/share/phpmyadmin>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

<Directory /usr/share/phpmyadmin/tmp>
    Require all granted
</Directory>
```

```bash
# Enable configuration
sudo a2enconf phpmyadmin
sudo systemctl reload apache2
```

## Solution 5: Check File Permissions

### Fix permissions:
```bash
# Set correct ownership
sudo chown -R www-data:www-data /usr/share/phpmyadmin

# Set correct permissions
sudo chmod -R 755 /usr/share/phpmyadmin
sudo chmod 644 /usr/share/phpmyadmin/config.inc.php
```

## Solution 6: Test PHP Processing

### Create a test PHP file:
```bash
# Create test file
echo "<?php phpinfo(); ?>" | sudo tee /var/www/html/test.php

# Set permissions
sudo chown www-data:www-data /var/www/html/test.php
sudo chmod 644 /var/www/html/test.php
```

### Test in browser:
```
http://09.94.58.89/test.php
```

If you see PHP information, PHP is working. If you see raw code, PHP isn't processing.

## Solution 7: Check Apache Configuration

### Verify Apache is configured for PHP:
```bash
# Check Apache configuration
sudo apache2ctl configtest

# Check if PHP is in Apache configuration
grep -r "php" /etc/apache2/
```

### Add PHP handler to Apache:
```bash
sudo nano /etc/apache2/mods-available/php8.1.conf
```

Make sure it contains:
```apache
<FilesMatch ".+\.ph(ar|p|tml)$">
    SetHandler application/x-httpd-php
</FilesMatch>
```

## Solution 8: Alternative - Use MySQL Command Line

### If phpMyAdmin continues to fail:
```bash
# Connect to MySQL directly
mysql -u root -p

# Show databases
SHOW DATABASES;

# Use bowling database
USE bowling_db;

# Show tables
SHOW TABLES;
```

## Quick Fix Commands

### Complete setup:
```bash
# Install PHP and modules
sudo apt update
sudo apt install php php-mysql php-common php-mbstring php-xml php-curl php-gd php-zip -y

# Enable PHP module
sudo a2enmod php8.1

# Restart Apache
sudo systemctl restart apache2

# Test PHP
echo "<?php echo 'PHP is working!'; ?>" | sudo tee /var/www/html/test.php
```

### Test in browser:
```
http://09.94.58.89/test.php
```

## Troubleshooting Steps

1. **Check PHP installation**: `php --version`
2. **Check Apache modules**: `apache2ctl -M | grep php`
3. **Check file permissions**: `ls -la /usr/share/phpmyadmin/`
4. **Check Apache logs**: `sudo tail -f /var/log/apache2/error.log`
5. **Test PHP processing**: Create test.php file
6. **Restart services**: `sudo systemctl restart apache2`

## Expected Result

After fixing, you should be able to access:
```
http://09.94.58.89/phpmyadmin/
```

And see the phpMyAdmin login page instead of raw PHP code.
