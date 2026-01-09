# 🔧 Fix Git Pull Error - Local Changes Conflict

## Problem

Error when pulling code:
```
error: Your local changes to the following files would be overwritten by merge:
storage/logs/debug.log
Please commit your changes or stash them before you merge.
```

**Cause:** `storage/logs/debug.log` is being tracked by Git, but it's a log file that changes locally and shouldn't be in version control.

## ✅ Solution: Remove Log File from Git

### Option 1: Stash Changes (Quick Fix)

**On cPanel Terminal:**

```bash
cd ~/booths.khbevents.com

# Stash local changes (saves them temporarily)
git stash

# Now pull should work
git pull origin main

# Optional: Apply stashed changes back (usually not needed for log files)
# git stash pop
```

### Option 2: Remove Log File from Git (Recommended)

**On cPanel Terminal:**

```bash
cd ~/booths.khbevents.com

# Remove the file from Git tracking (keeps the file locally)
git rm --cached storage/logs/debug.log

# Commit the removal
git commit -m "Remove debug.log from Git tracking"

# Push the change
git push origin main

# Now pull should work
git pull origin main
```

### Option 3: Reset the File (If you don't need local changes)

**On cPanel Terminal:**

```bash
cd ~/booths.khbevents.com

# Discard local changes to the log file
git checkout -- storage/logs/debug.log

# Now pull should work
git pull origin main
```

## 🔧 Permanent Fix: Update .gitignore

**On your local PC, update `.gitignore`:**

Make sure these lines exist:
```
/storage/logs/*.log
!/storage/logs/.gitkeep
```

Then commit and push:
```bash
git add .gitignore
git commit -m "Ensure log files are ignored"
git push origin main
```

## Quick Fix Command (Run on cPanel)

**Run this to fix immediately:**

```bash
cd ~/booths.khbevents.com && \
git stash && \
git pull origin main && \
echo "✓ Code updated successfully!"
```

## After Fixing

1. **Pull should work now**
2. **Log files won't cause conflicts anymore**

---

**The log file shouldn't be in Git. Stash or remove it!** 🧹
