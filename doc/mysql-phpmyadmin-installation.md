# MySQL with phpMyAdmin Installation Guide for Ubuntu on UpCloud Server

This guide will walk you through installing MySQL and phpMyAdmin on your Ubuntu UpCloud server.

## Prerequisites
- An UpCloud server running Ubuntu (18.04, 20.04, 22.04, or 24.04)
- Root or sudo access
- Basic command line knowledge

## Step 1: Update Your System

```bash
sudo apt update && sudo apt upgrade -y
```

## Step 2: Install MySQL Server

```bash
# Install MySQL server
sudo apt install mysql-server -y

# Start and enable MySQL service
sudo systemctl start mysql
sudo systemctl enable mysql

# Secure MySQL installation
sudo mysql_secure_installation
```

## Step 3: Install Web Server and PHP

```bash
# Install Apache and PHP
sudo apt install apache2 php php-mysql php-common php-mbstring php-xml php-curl php-gd php-zip unzip -y

# Start and enable Apache
sudo systemctl start apache2
sudo systemctl enable apache2
```

## Step 4: Install phpMyAdmin

```bash
# Install phpMyAdmin
sudo apt install phpmyadmin -y

# During installation, select Apache2 when prompted
# Choose 'Yes' when asked to configure database for phpMyAdmin
```

## Step 5: Configure Apache for phpMyAdmin

The installation should have already configured Apache. If not:

```bash
# Enable phpMyAdmin Apache configuration
sudo ln -s /etc/phpmyadmin/apache.conf /etc/apache2/conf-available/phpmyadmin.conf
sudo a2enconf phpmyadmin
sudo systemctl reload apache2
```

## Step 6: Configure phpMyAdmin

### Create phpMyAdmin configuration file:
```bash
sudo cp /usr/share/phpmyadmin/config.sample.inc.php /usr/share/phpmyadmin/config.inc.php
```

### Edit the configuration:
```bash
sudo nano /usr/share/phpmyadmin/config.inc.php
```

Add or modify these lines:
```php
$cfg['blowfish_secret'] = 'your-secret-key-here'; // Generate a random 32-character string
$cfg['Servers'][$i]['auth_type'] = 'cookie';
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;
```

## Step 7: Create MySQL User for phpMyAdmin

```bash
# Access MySQL as root
sudo mysql -u root -p

# Create a dedicated user for phpMyAdmin
CREATE USER 'phpmyadmin'@'localhost' IDENTIFIED BY 'your-secure-password';
GRANT ALL PRIVILEGES ON *.* TO 'phpmyadmin'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EXIT;
```

## Step 8: Test the Installation

1. Open your web browser and navigate to:
   ```
   http://your-server-ip/phpmyadmin
   ```

2. Login with:
   - Username: `phpmyadmin` (or `root`)
   - Password: The password you set

## Step 9: Security Recommendations

### Configure Firewall:
```bash
# Configure UFW firewall
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp
sudo ufw enable
```

### Secure phpMyAdmin:
```bash
# Restrict access to specific IP addresses (optional)
sudo nano /etc/apache2/conf-available/phpmyadmin.conf
# Add: Require ip your-ip-address
```

### Enable HTTPS (Recommended):
```bash
# Install SSL certificate (Let's Encrypt)
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d your-domain.com
```

## Troubleshooting

### Common Issues:

1. **phpMyAdmin not accessible:**
   - Check Apache status: `sudo systemctl status apache2`
   - Check Apache error logs: `sudo tail -f /var/log/apache2/error.log`

2. **MySQL connection issues:**
   - Check MySQL status: `sudo systemctl status mysql`
   - Verify MySQL is running: `sudo netstat -tlnp | grep 3306`

3. **Permission issues:**
   - Fix file permissions: `sudo chown -R www-data:www-data /usr/share/phpmyadmin`

### Useful Commands:
```bash
# Check service status
sudo systemctl status mysql
sudo systemctl status apache2

# View logs
sudo tail -f /var/log/mysql/error.log
sudo tail -f /var/log/apache2/error.log

# Restart services
sudo systemctl restart mysql
sudo systemctl restart apache2

# Check UFW status
sudo ufw status
```

## Next Steps

1. Create your first database
2. Import your existing data (if any)
3. Set up regular backups
4. Configure monitoring and logging
5. Consider setting up a reverse proxy for additional security

Your MySQL server with phpMyAdmin is now ready to use!
