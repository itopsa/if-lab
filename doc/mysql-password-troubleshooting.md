# MySQL Password Policy Troubleshooting

## Option 1: Check and Adjust MySQL Password Policy

If you're getting password policy errors, you can temporarily lower the password requirements:

```bash
# Access MySQL as root (if you can)
sudo mysql -u root

# Check current password policy
SHOW VARIABLES LIKE 'validate_password%';

# Temporarily lower password requirements
SET GLOBAL validate_password.policy=LOW;
SET GLOBAL validate_password.length=8;
SET GLOBAL validate_password.mixed_case_count=0;
SET GLOBAL validate_password.number_count=0;
SET GLOBAL validate_password.special_char_count=0;

# Exit MySQL
EXIT;
```

## Option 2: Use a Simple Password for Initial Setup

Try this simple password that should work with most policies:
```
phpMyAdmin2024!
```

## Option 3: Skip phpMyAdmin Database Configuration

If you continue having issues, you can install phpMyAdmin without automatic database configuration:

```bash
# Remove phpMyAdmin if partially installed
sudo apt remove --purge phpmyadmin -y
sudo apt autoremove -y

# Install phpMyAdmin without database configuration
sudo apt install phpmyadmin -y

# When prompted:
# - Choose "No" for database configuration
# - Select Apache2 when asked about web server
```

Then manually configure the database later.

## Option 4: Manual phpMyAdmin Installation

If automatic installation fails, install manually:

```bash
# Download phpMyAdmin
cd /tmp
wget https://www.phpmyadmin.net/downloads/phpMyAdmin-latest-all-languages.tar.gz
tar -xzf phpMyAdmin-latest-all-languages.tar.gz
sudo mv phpMyAdmin-*/ /usr/share/phpmyadmin

# Set permissions
sudo chown -R www-data:www-data /usr/share/phpmyadmin
sudo chmod 755 /usr/share/phpmyadmin/tmp
```

## Option 5: Reset MySQL Root Password

If you can't access MySQL at all:

```bash
# Stop MySQL
sudo systemctl stop mysql

# Start MySQL in safe mode
sudo mysqld_safe --skip-grant-tables --skip-networking &

# In another terminal, access MySQL
sudo mysql

# Reset root password
USE mysql;
UPDATE user SET authentication_string=PASSWORD('NewPassword123!') WHERE User='root';
FLUSH PRIVILEGES;
EXIT;

# Stop safe mode MySQL and restart normally
sudo pkill mysqld
sudo systemctl start mysql
```

## Recommended Next Steps:

1. Try the simple password: `phpMyAdmin2024!`
2. If that doesn't work, use Option 1 to adjust password policy
3. If still having issues, use Option 3 to skip automatic database configuration
4. Configure the database manually after installation
