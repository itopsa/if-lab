# SSH Troubleshooting for GitHub on Ubuntu Server

## Step 1: Check SSH Key Existence and Permissions

```bash
# Check if SSH key exists
ls -la ~/.ssh/

# If the key doesn't exist, create it
ssh-keygen -t ed25519 -C "your.email@example.com" -f ~/.ssh/github_ssh

# Set correct permissions
chmod 700 ~/.ssh
chmod 600 ~/.ssh/github_ssh
chmod 644 ~/.ssh/github_ssh.pub
```

## Step 2: Verify SSH Config File

```bash
# Check if SSH config exists
cat ~/.ssh/config

# If it doesn't exist or is wrong, create it
cat > ~/.ssh/config << EOF
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/github_ssh
    IdentitiesOnly yes
    AddKeysToAgent yes
EOF

# Set permissions for config
chmod 600 ~/.ssh/config
```

## Step 3: Start SSH Agent and Add Key

```bash
# Start SSH agent
eval "$(ssh-agent -s)"

# Add your key to the agent
ssh-add ~/.ssh/github_ssh

# Verify key is loaded
ssh-add -l
```

## Step 4: Test with Verbose Output

```bash
# Test SSH connection with detailed output
ssh -vT git@github.com
```

This will show you exactly what's happening during the connection attempt.

## Step 5: Check Your Public Key

```bash
# Display your public key
cat ~/.ssh/github_ssh.pub
```

**IMPORTANT:** Copy this entire output and add it to GitHub:
1. Go to GitHub.com → Settings → SSH and GPG keys
2. Click "New SSH key"
3. Paste the entire public key content
4. Give it a title like "Ubuntu Server"
5. Click "Add SSH key"

## Step 6: Alternative - Use HTTPS Instead

If SSH continues to fail, you can use HTTPS:

```bash
# Configure Git to use HTTPS
git config --global url."https://github.com/".insteadOf "git@github.com:"

# Clone using HTTPS
git clone https://github.com/itopsa/if-lab.git
```

## Step 7: Debug Common Issues

### Check if SSH agent is running:
```bash
echo $SSH_AUTH_SOCK
```

### Restart SSH agent if needed:
```bash
pkill ssh-agent
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/github_ssh
```

### Check GitHub's SSH fingerprints:
```bash
ssh-keyscan -t rsa github.com >> ~/.ssh/known_hosts
```

## Step 8: Test Connection Again

```bash
# Test SSH connection
ssh -T git@github.com
```

You should see: "Hi username! You've successfully authenticated..."

## Quick Fix Commands (Run in Order):

```bash
# 1. Create SSH key if it doesn't exist
ssh-keygen -t ed25519 -C "your.email@example.com" -f ~/.ssh/github_ssh -N ""

# 2. Set permissions
chmod 700 ~/.ssh
chmod 600 ~/.ssh/github_ssh
chmod 644 ~/.ssh/github_ssh.pub

# 3. Create SSH config
mkdir -p ~/.ssh
cat > ~/.ssh/config << EOF
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/github_ssh
    IdentitiesOnly yes
EOF
chmod 600 ~/.ssh/config

# 4. Start SSH agent and add key
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/github_ssh

# 5. Display public key to add to GitHub
echo "=== COPY THIS PUBLIC KEY TO GITHUB ==="
cat ~/.ssh/github_ssh.pub
echo "=== END PUBLIC KEY ==="

# 6. Test connection
ssh -T git@github.com
```

## If Still Failing:

1. **Check if you're behind a firewall/proxy**
2. **Verify GitHub is accessible:**
   ```bash
   ping github.com
   ```
3. **Try with different SSH key type:**
   ```bash
   ssh-keygen -t rsa -b 4096 -C "your.email@example.com" -f ~/.ssh/github_ssh
   ```
4. **Use GitHub CLI instead:**
   ```bash
   # Install GitHub CLI
   curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg | sudo dd of=/usr/share/keyrings/githubcli-archive-keyring.gpg
   echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | sudo tee /etc/apt/sources.list.d/github-cli.list > /dev/null
   sudo apt update && sudo apt install gh -y
   
   # Authenticate
   gh auth login
   ```
