# Finding Your Server's IP Address for Web Access

## Get IPv4 Address (Recommended for Web Access)

### Method 1: Using curl with IPv4
```bash
# Get IPv4 address specifically
curl -4 ifconfig.me

# Or try this alternative
curl -4 icanhazip.com
```

### Method 2: Using hostname
```bash
# Get all IP addresses
hostname -I

# This will show both IPv4 and IPv6 addresses
```

### Method 3: Using ip command
```bash
# Show all network interfaces
ip addr show

# Look for the IPv4 address (usually starts with 192.168, 10., or 172.)
```

### Method 4: Check specific interface
```bash
# If you know your network interface name (usually eth0 or ens3)
ip addr show eth0

# Or check all interfaces
ip route get 8.8.8.8 | awk '{print $7}'
```

## Understanding the Output

### IPv4 vs IPv6
- **IPv4**: Looks like `192.168.1.100` or `45.33.12.34`
- **IPv6**: Looks like `2605:7380:8000:1000:9c56:bcff:fed1:6f44`

### For Web Access
- **Use IPv4** for most web browsers and applications
- **IPv6** might not work in all browsers or networks

## Testing Your Web Interface

### Once you have your IPv4 address:

**Example URLs:**
```
http://YOUR_IPV4_ADDRESS/bowling-db/
```

**If your IPv4 is 45.33.12.34:**
```
http://45.33.12.34/bowling-db/
```

## Alternative: Use Domain Name

### If you have a domain name:
```bash
# Add to your domain's DNS
# Point your domain to your server's IP address

# Then access via:
http://yourdomain.com/bowling-db/
```

## Troubleshooting

### If you can't access the site:

1. **Check firewall:**
   ```bash
   sudo ufw status
   sudo ufw allow 80/tcp
   ```

2. **Check Apache is running:**
   ```bash
   sudo systemctl status apache2
   ```

3. **Test locally first:**
   ```bash
   curl -I http://localhost/bowling-db/
   ```

4. **Check if port 80 is open:**
   ```bash
   netstat -tlnp | grep :80
   ```

## Quick Commands to Try

```bash
# Get IPv4 address
curl -4 ifconfig.me

# Get all IPs
hostname -I

# Test local access
curl -I http://localhost/bowling-db/

# Test with your IP
curl -I http://YOUR_IP/bowling-db/
```

## Common IP Address Ranges

### Private IPs (Local Network):
- `192.168.x.x`
- `10.x.x.x`
- `172.16.x.x` to `172.31.x.x`

### Public IPs (Internet):
- Usually starts with different numbers
- Assigned by your hosting provider

## Security Note

- **Private IPs** are only accessible from your local network
- **Public IPs** are accessible from the internet
- Consider using HTTPS and firewall rules for public access
