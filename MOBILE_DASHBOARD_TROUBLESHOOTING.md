# Mobile Dashboard Troubleshooting Guide

## Issue: Mobile styles not showing in Chrome DevTools

### Quick Fixes:

1. **Hard Refresh the Page**
   - Windows: `Ctrl + Shift + R` or `Ctrl + F5`
   - Mac: `Cmd + Shift + R`
   - This clears cached CSS files

2. **Clear Browser Cache**
   - Open Chrome DevTools (F12)
   - Right-click the refresh button
   - Select "Empty Cache and Hard Reload"

3. **Verify Mobile Viewport**
   - In Chrome DevTools, make sure you're using a device preset (iPhone 14, etc.)
   - Check the viewport width shows 390px (iPhone 14) or similar
   - Make sure "Responsive" mode is NOT selected - use a specific device

4. **Check Console for Errors**
   - Open Chrome DevTools Console (F12 → Console tab)
   - Look for messages starting with "📱" or "✅"
   - Should see: "✅ Mobile styles FORCED - Width: 390" (or similar)

5. **Verify CSS Files Are Loading**
   - Open Chrome DevTools → Network tab
   - Refresh the page
   - Look for `mobile-design-system.css` and `global-mobile-enhancements.css`
   - Both should show status 200 (loaded successfully)

6. **Check Media Query**
   - In Chrome DevTools → Elements tab
   - Select the `<body>` element
   - Check Computed styles
   - Look for `background-color` - should be `#f5f7fa` on mobile

### What Should You See on Mobile:

1. **Purple/Blue Gradient Header** at the top with "Welcome back" and username
2. **2-column grid of stat cards** (8 cards total)
3. **White cards** with rounded corners (20px radius)
4. **Bottom navigation bar** with 5 tabs (Home, Booths, Bookings, Clients, More)
5. **Floating Action Button (FAB)** in bottom right corner
6. **No top navbar** (should be hidden)

### If Still Not Working:

1. Check if you're logged in (some styles might depend on auth)
2. Try in an incognito/private window
3. Check browser console for JavaScript errors
4. Verify the route is using `layouts.app` (not `layouts.adminlte`)

### Test Commands:

Open browser console and run:
```javascript
// Check viewport width
console.log('Width:', window.innerWidth);

// Check if mobile styles are applied
console.log('Body background:', getComputedStyle(document.body).backgroundColor);

// Check if mobile elements exist
console.log('App header:', document.querySelector('.app-header'));
console.log('Bottom nav:', document.querySelector('.app-bottom-nav'));
```

### Expected Results:
- Width: Should be ≤ 768px
- Body background: Should be `rgb(245, 247, 250)` (#f5f7fa)
- App header: Should exist and be visible
- Bottom nav: Should exist and be visible
