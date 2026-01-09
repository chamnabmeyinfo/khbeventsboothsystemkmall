# 🔍 Find Subdomain Document Root

## Problem

The subdomain `booths.khbevents.com` is not in `public_html/`. Let's find where it actually points.

## Step 1: Check Where Subdomain Points

**Run these commands on cPanel Terminal:**

```bash
# Check if subdomain directory exists in different locations
ls -la ~/booths.khbevents.com
ls -la ~/public_html/booths.khbevents.com
ls -la ~/domains/booths.khbevents.com

# Check what's in the subdomain directory
ls -la ~/booths.khbevents.com | head -20
```

## Step 2: Check Apache Configuration

**Check Apache virtual host configuration:**

```bash
# Check if there's a virtual host config
grep -r "booths.khbevents.com" /etc/apache2/conf.d/ 2>/dev/null
grep -r "booths.khbevents.com" /etc/httpd/conf.d/ 2>/dev/null
```

## Step 3: Check cPanel Subdomain Settings

**In cPanel:**
1. Go to **Subdomains**
2. Find `booths.khbevents.com`
3. Check the **Document Root** field
4. Note the exact path shown

## Step 4: Common Scenarios

### Scenario A: Subdomain points to custom directory

If document root shows: `/home/khbevents/booths.khbevents.com`

**Fix:** Change it to: `/home/khbevents/booths.khbevents.com/public`

### Scenario B: Subdomain uses symbolic link

```bash
# Check if it's a symlink
file ~/public_html/booths.khbevents.com
ls -la ~/public_html/ | grep booths
```

### Scenario C: Subdomain in different location

Some cPanel setups use:
- `~/domains/booths.khbevents.com/public_html`
- `~/subdomains/booths.khbevents.com/public_html`

## Quick Diagnostic Script

**Run this to find the subdomain:**

```bash
echo "=== Checking common subdomain locations ==="
echo "1. ~/booths.khbevents.com:"
test -d ~/booths.khbevents.com && echo "   ✓ EXISTS" || echo "   ✗ NOT FOUND"

echo "2. ~/public_html/booths.khbevents.com:"
test -d ~/public_html/booths.khbevents.com && echo "   ✓ EXISTS" || echo "   ✗ NOT FOUND"

echo "3. ~/domains/booths.khbevents.com:"
test -d ~/domains/booths.khbevents.com && echo "   ✓ EXISTS" || echo "   ✗ NOT FOUND"

echo "=== Checking if Git cloned to custom location ==="
find ~ -maxdepth 3 -type d -name "booths.khbevents.com" 2>/dev/null
```

---

**Run the diagnostic script first to find where your subdomain actually is!**
