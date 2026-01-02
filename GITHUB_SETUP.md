# GitHub Setup Instructions

## Step 1: Create GitHub Repository

1. Go to [GitHub](https://github.com) and sign in
2. Click the "+" icon in the top right corner
3. Select "New repository"
4. Fill in the repository details:
   - **Repository name**: `kmallxmas-laravel` (or your preferred name)
   - **Description**: "KHB Events - K Mall Xmas Booth Booking System (Laravel)"
   - **Visibility**: Choose Private (recommended) or Public
   - **DO NOT** initialize with README, .gitignore, or license (we already have these)
5. Click "Create repository"

## Step 2: Connect Local Repository to GitHub

After creating the repository on GitHub, you'll see a page with setup instructions. Use these commands:

### If you haven't created the repository yet:

```bash
# Add the remote repository (replace YOUR_USERNAME with your GitHub username)
git remote add origin https://github.com/YOUR_USERNAME/kmallxmas-laravel.git

# Rename branch to main (if needed)
git branch -M main

# Push to GitHub
git push -u origin main
```

### If you already have commits:

```bash
# Add the remote
git remote add origin https://github.com/YOUR_USERNAME/kmallxmas-laravel.git

# Push your code
git push -u origin main
```

## Step 3: Verify

After pushing, refresh your GitHub repository page. You should see all your files there!

## Alternative: Using SSH (if you have SSH keys set up)

```bash
git remote add origin git@github.com:YOUR_USERNAME/kmallxmas-laravel.git
git push -u origin main
```

## Troubleshooting

### If you get "remote origin already exists":
```bash
git remote remove origin
git remote add origin https://github.com/YOUR_USERNAME/kmallxmas-laravel.git
```

### If you need to force push (use with caution):
```bash
git push -u origin main --force
```

### If authentication fails:
- Use GitHub Personal Access Token instead of password
- Or set up SSH keys for easier authentication

## Next Steps

After pushing to GitHub:
1. Add collaborators (Settings > Collaborators)
2. Set up branch protection rules (if needed)
3. Configure GitHub Actions for CI/CD (optional)
4. Add issues and project boards (optional)
