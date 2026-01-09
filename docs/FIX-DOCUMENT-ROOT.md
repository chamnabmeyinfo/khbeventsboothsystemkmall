# 🔧 Fix Document Root - Critical Step

## Problem

Getting 404 error for `https://booths.khbevents.com/test.php` means the **Document Root is NOT pointing to the `public/` directory**.

## ✅ Solution: Fix Document Root in cPanel

### Step 1: Check Current Document Root

**In cPanel:**
1. Go to **Subdomains** (under "Domains" section)
2. Find `booths.khbevents.com`
3. Look at the **Document Root** path

**It's probably showing:**
```
/home/khbevents/booths.khbevents.com
```

**But it SHOULD be:**
```
/home/khbevents/booths.khbevents.com/public
```

### Step 2: Change Document Root

1. In **Subdomains**, click **Manage** next to `booths.khbevents.com`
2. You'll see a field for **Document Root**
3. Change it to:
   ```
   public_html/booths.khbevents.com/public
   ```
   OR the full path:
   ```
   /home/khbevents/booths.khbevents.com/public
   ```
4. Click **Change** or **Save**
5. Wait 1-2 minutes for changes to take effect

### Step 3: Verify Document Root

**Option A: Check in cPanel**
- Go back to **Subdomains**
- Verify it shows: `public_html/booths.khbevents.com/public`

**Option B: Check via Terminal**
```bash
# Check where the subdomain actually points
ls -la ~/public_html/booths.khbevents.com
```

### Step 4: Test Again

1. **Clear browser cache** (Ctrl+F5 or Cmd+Shift+R)
2. **Try:** `https://booths.khbevents.com/test.php`
3. **Should work now!**

## Alternative: If You Can't Change Document Root

If cPanel doesn't allow changing document root, create a symbolic link:

```bash
# Remove existing directory if it exists
rm -rf ~/public_html/booths.khbevents.com

# Create symbolic link to public directory
ln -s ~/booths.khbevents.com/public ~/public_html/booths.khbevents.com
```

## Quick Diagnostic

**Run this to check:**
```bash
# Check if public_html/booths.khbevents.com exists
ls -la ~/public_html/booths.khbevents.com

# Check if it's a symlink or directory
file ~/public_html/booths.khbevents.com

# Check if index.php exists in the document root
ls -la ~/public_html/booths.khbevents.com/index.php
```

## Expected Result

After fixing document root:
- ✅ `https://booths.khbevents.com/test.php` should work
- ✅ `https://booths.khbevents.com/` should load Laravel
- ✅ `https://booths.khbevents.com/login` should work

---

**The document root MUST point to the `public/` directory!** 🔧
