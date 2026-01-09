# ⚡ Quick Fix for Git Pull Error on cPanel

## Problem

When pulling code in cPanel, you get:
```
error: Your local changes to the following files would be overwritten by merge:
storage/logs/debug.log
```

## ✅ Quick Fix (Run on cPanel Terminal)

**Option 1: Stash and Pull (Fastest)**

```bash
cd ~/booths.khbevents.com
git stash
git pull origin main
```

**Option 2: Remove Log File from Git**

```bash
cd ~/booths.khbevents.com
git rm --cached storage/logs/debug.log
git commit -m "Remove debug.log from Git"
git push origin main
git pull origin main
```

**Option 3: Reset and Pull**

```bash
cd ~/booths.khbevents.com
git checkout -- storage/logs/debug.log
git pull origin main
```

## 🎯 Recommended: Use Option 1 (Stash)

It's the fastest and safest:

```bash
cd ~/booths.khbevents.com && git stash && git pull origin main
```

---

**After this, Git pull will work!** ✅
