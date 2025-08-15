# Fix MySQL Database Connection Error

## Error: Access denied for user 'root'@'localhost' (using password: NO)

This error means the web application is trying to connect to MySQL without a password, but your MySQL root user requires a password.

## Solution 1: Update Database Configuration

### Edit the config.php file:
```bash
# Edit the configuration file
sudo nano /var/www/html/bowling-db/config.php
```

### Update the database configuration:
```php
$config = [
    'host' => 'localhost',
    'dbname' => 'bowling_db',
    'username' => 'root',
    'password' => 'YOUR_MYSQL_ROOT_PASSWORD',  // Add your actual MySQL root password here
    'charset' => 'utf8mb4'
];
```

## Solution 2: Create a Dedicated Database User (Recommended)

### Connect to MySQL as root:
```bash
mysql -u root -p
```

### Create a new user for the web application:
```sql
-- Create a new user
CREATE USER 'bowling_web'@'localhost' IDENTIFIED BY 'secure_password_123';

-- Grant permissions to the bowling database
GRANT SELECT, INSERT, UPDATE, DELETE ON bowling_db.* TO 'bowling_web'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;

-- Exit MySQL
EXIT;
```

### Update config.php with the new user:
```bash
sudo nano /var/www/html/bowling-db/config.php
```

```php
$config = [
    'host' => 'localhost',
    'dbname' => 'bowling_db',
    'username' => 'bowling_web',
    'password' => 'secure_password_123',
    'charset' => 'utf8mb4'
];
```

## Solution 3: Reset MySQL Root Password

### If you don't remember your MySQL root password:

#### Stop MySQL:
```bash
sudo systemctl stop mysql
```

#### Start MySQL in safe mode:
```bash
sudo mysqld_safe --skip-grant-tables --skip-networking &
```

#### Connect to MySQL:
```bash
mysql -u root
```

#### Reset the password:
```sql
USE mysql;
UPDATE user SET authentication_string=PASSWORD('new_password_123') WHERE User='root';
FLUSH PRIVILEGES;
EXIT;
```

#### Stop safe mode and restart MySQL:
```bash
sudo pkill mysqld
sudo systemctl start mysql
```

#### Test the new password:
```bash
mysql -u root -p
```

## Solution 4: Allow Root Access Without Password (Not Recommended for Production)

### Connect to MySQL:
```bash
mysql -u root -p
```

### Update root user to allow no password:
```sql
USE mysql;
UPDATE user SET plugin='mysql_native_password' WHERE User='root';
UPDATE user SET authentication_string='' WHERE User='root';
FLUSH PRIVILEGES;
EXIT;
```

### Update config.php:
```php
$config = [
    'host' => 'localhost',
    'dbname' => 'bowling_db',
    'username' => 'root',
    'password' => '',  // Empty password
    'charset' => 'utf8mb4'
];
```

## Test Database Connection

### Test from command line:
```bash
# Test with root user
mysql -u root -p bowling_db -e "SELECT 1;"

# Or test with new user
mysql -u bowling_web -p bowling_db -e "SELECT 1;"
```

### Test from web interface:
After updating config.php, refresh your browser:
```
http://85.9.197.228/bowling-db/
```

## Troubleshooting

### Check MySQL Status:
```bash
sudo systemctl status mysql
```

### Check MySQL Error Logs:
```bash
sudo tail -f /var/log/mysql/error.log
```

### Verify Database Exists:
```bash
mysql -u root -p -e "SHOW DATABASES;"
```

### Check User Permissions:
```bash
mysql -u root -p -e "SELECT User, Host FROM mysql.user;"
```

## Security Recommendations

1. **Use a dedicated database user** (not root)
2. **Use strong passwords**
3. **Limit user permissions** to only what's needed
4. **Use SSL connections** in production
5. **Regular password updates**

## Quick Fix Commands

```bash
# 1. Create dedicated user
mysql -u root -p -e "CREATE USER 'bowling_web'@'localhost' IDENTIFIED BY 'secure_password_123'; GRANT SELECT ON bowling_db.* TO 'bowling_web'@'localhost'; FLUSH PRIVILEGES;"

# 2. Update config file
sudo sed -i "s/'password' => '',/'password' => 'secure_password_123',/" /var/www/html/bowling-db/config.php
sudo sed -i "s/'username' => 'root',/'username' => 'bowling_web',/" /var/www/html/bowling-db/config.php

# 3. Test connection
mysql -u bowling_web -p bowling_db -e "SELECT 1;"
```

## After Fixing

1. **Refresh your browser** at `http://85.9.197.228/bowling-db/`
2. **Check for any remaining errors**
3. **Test all pages** (Dashboard, Bowlers, Series, etc.)
4. **Verify data is displaying** correctly
