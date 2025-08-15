# Fix PHP Not Loaded in Apache

## Issue: No PHP modules found in Apache

The command `apache2ctl -M | grep php` returned nothing, which means PHP modules are not loaded in Apache.

## Step 1: Check PHP Installation

```bash
# Check if PHP is installed
php --version

# If PHP is not installed, install it
sudo apt update
sudo apt install php php-mysql php-common php-mbstring php-xml php-curl php-gd php-zip -y
```

## Step 2: Check Available PHP Modules

```bash
# List available PHP modules
ls /etc/apache2/mods-available/ | grep php

# You should see something like:
# php8.1.conf
# php8.1.load
```

## Step 3: Enable PHP Module

```bash
# Enable PHP module for Apache
sudo a2enmod php8.1

# Check if it was enabled
ls /etc/apache2/mods-enabled/ | grep php
```

## Step 4: Restart Apache

```bash
# Restart Apache to load the PHP module
sudo systemctl restart apache2

# Check Apache status
sudo systemctl status apache2
```

## Step 5: Verify PHP Module is Loaded

```bash
# Check if PHP module is now loaded
apache2ctl -M | grep php

# You should see something like:
# php8_module (shared)
```

## Step 6: Test PHP Processing

```bash
# Create a test PHP file
echo "<?php echo 'PHP is working!'; ?>" | sudo tee /var/www/html/test.php

# Set permissions
sudo chown www-data:www-data /var/www/html/test.php
sudo chmod 644 /var/www/html/test.php
```

## Step 7: Test in Browser

Go to: `http://09.94.58.89/test.php`

You should see: "PHP is working!" instead of raw PHP code.

## Alternative: Use PHP-FPM

If the above doesn't work, try PHP-FPM:

```bash
# Install PHP-FPM
sudo apt install php-fpm -y

# Enable required modules
sudo a2enmod proxy_fcgi setenvif

# Enable PHP-FPM configuration
sudo a2enconf php8.1-fpm

# Restart Apache
sudo systemctl restart apache2
```

## Troubleshooting

### Check Apache Configuration
```bash
# Test Apache configuration
sudo apache2ctl configtest

# Check Apache error logs
sudo tail -f /var/log/apache2/error.log
```

### Check PHP Module Files
```bash
# Check if PHP module files exist
ls -la /etc/apache2/mods-available/php*

# Check if PHP module is enabled
ls -la /etc/apache2/mods-enabled/php*
```

### Manual PHP Module Configuration
If automatic enabling doesn't work:

```bash
# Create symbolic link manually
sudo ln -s /etc/apache2/mods-available/php8.1.load /etc/apache2/mods-enabled/
sudo ln -s /etc/apache2/mods-available/php8.1.conf /etc/apache2/mods-enabled/

# Restart Apache
sudo systemctl restart apache2
```

## Quick Fix Commands (Copy and Paste)

```bash
# 1. Install PHP
sudo apt update
sudo apt install php php-mysql php-common php-mbstring php-xml php-curl php-gd php-zip -y

# 2. Enable PHP module
sudo a2enmod php8.1

# 3. Restart Apache
sudo systemctl restart apache2

# 4. Verify PHP is loaded
apache2ctl -M | grep php

# 5. Test PHP
echo "<?php echo 'PHP is working!'; ?>" | sudo tee /var/www/html/test.php
sudo chown www-data:www-data /var/www/html/test.php
sudo chmod 644 /var/www/html/test.php
```

## Expected Results

After running the commands:

1. **PHP module should be loaded**: `apache2ctl -M | grep php` should show PHP module
2. **Test page should work**: `http://09.94.58.89/test.php` should show "PHP is working!"
3. **phpMyAdmin should work**: `http://09.94.58.89/phpmyadmin/` should show login page

## If Still Not Working

### Check PHP Version
```bash
# Check what PHP version is installed
php --version

# If it's not 8.1, adjust the module name
# For PHP 8.0: sudo a2enmod php8.0
# For PHP 7.4: sudo a2enmod php7.4
```

### Alternative PHP Installation
```bash
# Remove existing PHP
sudo apt remove --purge php* -y
sudo apt autoremove -y

# Install PHP with Apache
sudo apt install apache2 php libapache2-mod-php php-mysql -y

# Enable PHP module
sudo a2enmod php8.1
sudo systemctl restart apache2
```
