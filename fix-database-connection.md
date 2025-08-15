# Fix MySQL Connection Error - Step by Step

## Current Error: Access denied for user 'root'@'localhost' (using password: NO)

This means the web application is trying to connect to MySQL without a password, but your MySQL requires one.

## Step 1: Check Current Configuration

```bash
# View the current config file
sudo cat /var/www/html/bowling-db/config.php
```

## Step 2: Update the Configuration File

```bash
# Edit the configuration file
sudo nano /var/www/html/bowling-db/config.php
```

### Find this section:
```php
$config = [
    'host' => 'localhost',
    'dbname' => 'bowling_db',
    'username' => 'root',
    'password' => '',  // This is empty!
    'charset' => 'utf8mb4'
];
```

### Change it to (Option A - Use Root Password):
```php
$config = [
    'host' => 'localhost',
    'dbname' => 'bowling_db',
    'username' => 'root',
    'password' => 'YOUR_MYSQL_ROOT_PASSWORD',  // Add your actual MySQL root password here
    'charset' => 'utf8mb4'
];
```

### OR Change it to (Option B - Create New User):
```php
$config = [
    'host' => 'localhost',
    'dbname' => 'bowling_db',
    'username' => 'bowling_web',
    'password' => 'secure_password_123',
    'charset' => 'utf8mb4'
];
```

## Step 3: If Using Option B (Recommended), Create the User

```bash
# Connect to MySQL as root
mysql -u root -p

# Create the new user
CREATE USER 'bowling_web'@'localhost' IDENTIFIED BY 'secure_password_123';
GRANT SELECT, INSERT, UPDATE, DELETE ON bowling_db.* TO 'bowling_web'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Step 4: Test the Connection

```bash
# Test with root user (if using Option A)
mysql -u root -p bowling_db -e "SELECT 1;"

# OR test with new user (if using Option B)
mysql -u bowling_web -p bowling_db -e "SELECT 1;"
```

## Step 5: Restart Web Server

```bash
# Restart Apache
sudo systemctl restart apache2

# Check status
sudo systemctl status apache2
```

## Step 6: Test the Web Interface

Open your browser and go to:
```
http://85.9.197.228/bowling-db/
```

## Quick Fix Commands (Copy and Paste)

### If you know your MySQL root password:
```bash
# Edit config file
sudo nano /var/www/html/bowling-db/config.php
# Change 'password' => '', to 'password' => 'YOUR_PASSWORD',
```

### If you want to create a new user:
```bash
# Create user in MySQL
mysql -u root -p -e "CREATE USER 'bowling_web'@'localhost' IDENTIFIED BY 'secure_password_123'; GRANT SELECT ON bowling_db.* TO 'bowling_web'@'localhost'; FLUSH PRIVILEGES;"

# Update config file
sudo sed -i "s/'password' => '',/'password' => 'secure_password_123',/" /var/www/html/bowling-db/config.php
sudo sed -i "s/'username' => 'root',/'username' => 'bowling_web',/" /var/www/html/bowling-db/config.php

# Restart Apache
sudo systemctl restart apache2
```

## Troubleshooting

### Check MySQL Status:
```bash
sudo systemctl status mysql
```

### Check if Database Exists:
```bash
mysql -u root -p -e "SHOW DATABASES;"
```

### Check User Permissions:
```bash
mysql -u root -p -e "SELECT User, Host FROM mysql.user;"
```

### Check Apache Error Logs:
```bash
sudo tail -f /var/log/apache2/error.log
```

## Common Issues

### 1. "Access denied" - Wrong Password
- Double-check your MySQL root password
- Try connecting manually: `mysql -u root -p`

### 2. "Database doesn't exist"
- Create the database: `mysql -u root -p -e "CREATE DATABASE bowling_db;"`

### 3. "User doesn't exist"
- Create the user as shown in Step 3

### 4. "Permission denied"
- Check file permissions: `sudo chown -R www-data:www-data /var/www/html/bowling-db/`

## After Fixing

1. **Refresh your browser** at `http://85.9.197.228/bowling-db/`
2. **You should see** the bowling database dashboard
3. **Test all pages** (Dashboard, Bowlers, Series, etc.)
4. **Check for any remaining errors**

## Security Note

- **Option B (dedicated user)** is more secure than using root
- **Change the password** to something strong
- **Limit permissions** to only what's needed
