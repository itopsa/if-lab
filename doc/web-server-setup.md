# Web Server Configuration for Bowling Database Interface

## Option 1: Apache Web Server (Most Common)

### Install Apache
```bash
# Update package list
sudo apt update

# Install Apache
sudo apt install apache2 -y

# Start and enable Apache
sudo systemctl start apache2
sudo systemctl enable apache2

# Check status
sudo systemctl status apache2
```

### Configure Apache Virtual Host

#### Method 1: Copy to Default Directory
```bash
# Copy web files to Apache's default directory
sudo cp -r web/ /var/www/html/bowling-db/

# Set proper permissions
sudo chown -R www-data:www-data /var/www/html/bowling-db/
sudo chmod -R 755 /var/www/html/bowling-db/
sudo chmod 644 /var/www/html/bowling-db/*.php
sudo chmod 644 /var/www/html/bowling-db/pages/*.php

# Access at: http://your-server-ip/bowling-db/
```

#### Method 2: Create Custom Virtual Host
```bash
# Create virtual host configuration
sudo nano /etc/apache2/sites-available/bowling-db.conf
```

Add this content:
```apache
<VirtualHost *:80>
    ServerName bowling-db.local
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/bowling-db
    
    <Directory /var/www/bowling-db>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/bowling-db_error.log
    CustomLog ${APACHE_LOG_DIR}/bowling-db_access.log combined
</VirtualHost>
```

```bash
# Copy files to custom directory
sudo mkdir -p /var/www/bowling-db
sudo cp -r web/* /var/www/bowling-db/

# Set permissions
sudo chown -R www-data:www-data /var/www/bowling-db/
sudo chmod -R 755 /var/www/bowling-db/

# Enable the site
sudo a2ensite bowling-db.conf
sudo systemctl reload apache2

# Add to hosts file (optional)
echo "127.0.0.1 bowling-db.local" | sudo tee -a /etc/hosts
```

### Configure PHP
```bash
# Install PHP and MySQL extension
sudo apt install php php-mysql php-common php-mbstring php-xml php-curl php-gd php-zip -y

# Restart Apache
sudo systemctl restart apache2
```

## Option 2: Nginx Web Server

### Install Nginx
```bash
# Update package list
sudo apt update

# Install Nginx
sudo apt install nginx -y

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### Configure Nginx
```bash
# Create Nginx configuration
sudo nano /etc/nginx/sites-available/bowling-db
```

Add this content:
```nginx
server {
    listen 80;
    server_name bowling-db.local;
    root /var/www/bowling-db;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

```bash
# Copy files
sudo mkdir -p /var/www/bowling-db
sudo cp -r web/* /var/www/bowling-db/

# Set permissions
sudo chown -R www-data:www-data /var/www/bowling-db/
sudo chmod -R 755 /var/www/bowling-db/

# Enable site
sudo ln -s /etc/nginx/sites-available/bowling-db /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Install PHP-FPM
```bash
# Install PHP-FPM
sudo apt install php-fpm php-mysql -y

# Restart Nginx
sudo systemctl restart nginx
```

## Option 3: Built-in PHP Development Server (Quick Test)

```bash
# Navigate to web directory
cd web/

# Start PHP development server
php -S 0.0.0.0:8080

# Access at: http://your-server-ip:8080/
```

## Option 4: XAMPP/LAMPP (All-in-One)

### Install XAMPP
```bash
# Download XAMPP
wget https://sourceforge.net/projects/xampp/files/XAMPP%20Linux/8.2.4/xampp-linux-x64-8.2.4-0-installer.run

# Make executable
chmod +x xampp-linux-x64-8.2.4-0-installer.run

# Install
sudo ./xampp-linux-x64-8.2.4-0-installer.run
```

### Configure XAMPP
```bash
# Copy files to htdocs
sudo cp -r web/ /opt/lampp/htdocs/bowling-db/

# Set permissions
sudo chown -R daemon:daemon /opt/lampp/htdocs/bowling-db/
sudo chmod -R 755 /opt/lampp/htdocs/bowling-db/

# Start XAMPP
sudo /opt/lampp/lampp start

# Access at: http://your-server-ip/bowling-db/
```

## Firewall Configuration

### UFW (Ubuntu's default firewall)
```bash
# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 8080/tcp  # If using PHP dev server

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

### iptables
```bash
# Allow HTTP traffic
sudo iptables -A INPUT -p tcp --dport 80 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 443 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 8080 -j ACCEPT  # If using PHP dev server
```

## Database Configuration

### Update Database Connection
Edit `web/config.php`:
```php
$config = [
    'host' => 'localhost',
    'dbname' => 'bowling_db',
    'username' => 'root',  // Or create dedicated user
    'password' => 'your_mysql_password',
    'charset' => 'utf8mb4'
];
```

### Create Dedicated Database User (Recommended)
```sql
-- Connect to MySQL as root
mysql -u root -p

-- Create dedicated user
CREATE USER 'bowling_web'@'localhost' IDENTIFIED BY 'secure_password';
GRANT SELECT ON bowling_db.* TO 'bowling_web'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Then update `config.php`:
```php
$config = [
    'host' => 'localhost',
    'dbname' => 'bowling_db',
    'username' => 'bowling_web',
    'password' => 'secure_password',
    'charset' => 'utf8mb4'
];
```

## Testing Your Setup

### 1. Test Web Server
```bash
# Test Apache
curl -I http://localhost/

# Test Nginx
curl -I http://localhost/
```

### 2. Test PHP
```bash
# Create test file
echo "<?php phpinfo(); ?>" | sudo tee /var/www/html/test.php

# Access: http://your-server-ip/test.php
```

### 3. Test Database Connection
```bash
# Test MySQL connection
mysql -u bowling_web -p bowling_db -e "SELECT 1;"
```

### 4. Test Bowling Interface
Access: `http://your-server-ip/bowling-db/`

## Troubleshooting

### Common Issues

1. **Permission Denied:**
   ```bash
   sudo chown -R www-data:www-data /var/www/bowling-db/
   sudo chmod -R 755 /var/www/bowling-db/
   ```

2. **Database Connection Error:**
   - Check MySQL is running: `sudo systemctl status mysql`
   - Verify credentials in `config.php`
   - Test connection: `mysql -u username -p`

3. **Page Not Found:**
   - Check web server is running
   - Verify file permissions
   - Check error logs: `sudo tail -f /var/log/apache2/error.log`

4. **PHP Errors:**
   - Enable error display in `config.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

### Check Logs
```bash
# Apache logs
sudo tail -f /var/log/apache2/error.log
sudo tail -f /var/log/apache2/access.log

# Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# PHP logs
sudo tail -f /var/log/php8.1-fpm.log
```

## Security Considerations

1. **Use HTTPS in production**
2. **Create dedicated database user**
3. **Restrict file permissions**
4. **Keep software updated**
5. **Use firewall rules**
6. **Regular backups**

## Quick Setup Summary

For a quick setup on Ubuntu:
```bash
# Install Apache and PHP
sudo apt update
sudo apt install apache2 php php-mysql -y

# Copy files
sudo cp -r web/ /var/www/html/bowling-db/

# Set permissions
sudo chown -R www-data:www-data /var/www/html/bowling-db/
sudo chmod -R 755 /var/www/html/bowling-db/

# Configure database connection
sudo nano /var/www/html/bowling-db/config.php

# Access at: http://your-server-ip/bowling-db/
```
