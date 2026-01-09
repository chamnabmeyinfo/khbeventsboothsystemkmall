# GitHub Push Setup Guide

This guide explains how to set up GitHub push functionality using `.env` configuration.

## 📋 Prerequisites

1. A GitHub account
2. A Personal Access Token (PAT) with `repo` scope
   - Generate at: https://github.com/settings/tokens
   - For private repos: select `repo` scope
   - For public repos: select `public_repo` scope

## 🚀 Quick Setup

### Step 1: Create .env File

Copy the example file and fill in your credentials:

```powershell
Copy-Item .env.example .env
```

### Step 2: Edit .env File

Open `.env` and fill in your GitHub credentials:

```env
GITHUB_USERNAME=your_github_username
GITHUB_TOKEN=your_personal_access_token
GITHUB_REPO_URL=https://github.com/chamnabmeyinfo/khbevents-boothsystem-kmall.git
GITHUB_USE_SSH=false
```

### Step 3: Run Setup Script

```powershell
.\git-setup.ps1
```

This will configure your git remote to use credentials from `.env`.

### Step 4: Push to GitHub

Use the helper script to push changes:

```powershell
# Push with default message
.\git-push.ps1

# Push with custom message
.\git-push.ps1 -Message "Your commit message"

# Push to specific branch
.\git-push.ps1 -Message "Your commit message" -Branch "main"
```

## 📝 Manual Git Commands

If you prefer to use git commands directly, the remote is already configured with credentials:

```powershell
# Stage all changes
git add -A

# Commit changes
git commit -m "Your commit message"

# Push to GitHub
git push origin main
```

## 🔐 Security Notes

1. **Never commit `.env` file** - It's already in `.gitignore`
2. **Keep your token secure** - Don't share it or commit it
3. **Rotate tokens regularly** - Update your token in `.env` if compromised
4. **Use SSH for better security** - Set `GITHUB_USE_SSH=true` and configure SSH keys

## 🔄 Using SSH Instead

For better security, you can use SSH keys:

1. Generate SSH key (if you don't have one):
   ```powershell
   ssh-keygen -t ed25519 -C "your_email@example.com"
   ```

2. Add SSH key to GitHub:
   - Copy public key: `cat ~/.ssh/id_ed25519.pub`
   - Add at: https://github.com/settings/keys

3. Update `.env`:
   ```env
   GITHUB_USE_SSH=true
   ```

4. Run setup again:
   ```powershell
   .\git-setup.ps1
   ```

## 🛠️ Troubleshooting

### Error: "could not read Username"
- Make sure `.env` file exists and has correct credentials
- Run `.\git-setup.ps1` to reconfigure remote

### Error: "Authentication failed"
- Check if your token is valid and has correct scopes
- Generate a new token if needed

### Error: "Permission denied"
- Verify your token has `repo` scope for private repos
- Check if you have push access to the repository

## 📚 Additional Resources

- [GitHub Personal Access Tokens](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token)
- [GitHub SSH Keys](https://docs.github.com/en/authentication/connecting-to-github-with-ssh)
