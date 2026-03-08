# Floor Plan Designer UI Guide

**Last Updated:** March 7, 2026

---

## Overview

The Floor Plan Designer (`/booths?view=canvas&floor_plan_id=<id>`) provides a visual canvas for placing and managing booths on floor plan images. This guide documents the UI layout, styling, and key files updated in the March 2026 redesign.

---

## Layout Structure

### Main Row: 20% / 80% Split

The `designer-main-row` uses CSS Grid for a side-by-side layout:

| Area | Width | Content |
|------|-------|---------|
| **Sidebar (left)** | 20% | Booth numbers by zone, floor plan selector, stats |
| **Canvas (right)** | 80% | Floor plan image, draggable booths, zoom/pan |

```
┌─────────────────────────────────────────────────────────────┐
│  designer-main-row (CSS Grid: 20% 1fr)                       │
├──────────────┬──────────────────────────────────────────────┤
│ sidebar-     │  canvas-container                             │
│ content      │  ┌──────────────────────────────────────────┐ │
│              │  │  canvas-wrapper (floor plan image)       │ │
│ • Zones      │  │  + draggable booth elements              │ │
│ • Booths     │  └──────────────────────────────────────────┘ │
│ • Stats      │                                                │
└──────────────┴──────────────────────────────────────────────┘
```

### Responsive Behavior

- **Desktop:** 20% sidebar, 80% canvas.
- **Tablet/small:** Sidebar can collapse or stack; canvas remains primary.
- **Mobile:** Layout adapts for smaller screens (see media queries in CSS).

---

## Key Files

| File | Purpose |
|------|---------|
| `resources/views/booths/index.blade.php` | Main Blade template; includes cache-busted CSS link. |
| `public/css/floor-plan-designer.css` | All designer layout and styling (grid, sidebar, canvas, zones, booths). |
| `public/js/floor-plan-designer.js` | Canvas logic, booth drag/drop, zoom, save positions. |

---

## CSS Design Tokens

The designer uses CSS variables (Photoshop-style) for consistent theming:

| Variable | Purpose |
|----------|---------|
| `--fpd-sidebar-bg` | Sidebar background |
| `--fpd-sidebar-border` | Sidebar border color |
| `--fpd-canvas-bg` | Canvas area background |
| `--fpd-zone-header-bg` | Zone section header |
| `--fpd-booth-item-bg` | Booth number item background |

These are defined in `:root` in `floor-plan-designer.css` and can be overridden for theming.

---

## Asset Cache Busting

To force browsers to reload CSS/JS after changes:

1. **Config:** `config/app.php` has `asset_version` (env `ASSET_VERSION`).
2. **Blade:** CSS link uses `?v={{ config('app.asset_version') ?? filemtime(...) }}`.
3. **Env:** Set `ASSET_VERSION=2` (or increment) in `.env` during development.
4. **Production:** Bump `ASSET_VERSION` when deploying; or rely on `filemtime()` if not set.

---

## Deployment Notes

### Bootstrap Cache (Production 500 Fix)

If the site returns 500 after `git pull` with "Class Spatie\LaravelIgnition\IgnitionServiceProvider not found":

- **Cause:** Bootstrap cache was committed from a dev environment (with `spatie/laravel-ignition`). Production runs `composer install --no-dev`, so Ignition is not installed.
- **Fix:** Delete on server:
  - `bootstrap/cache/config.php`
  - `bootstrap/cache/packages.php`
  - `bootstrap/cache/services.php`
- Laravel will regenerate them for the production environment.

### PCRE Limits (Large Blade)

The booth index view is large (~950KB). If Blade compilation fails silently, add to `public/.user.ini`:

```ini
pcre.backtrack_limit = 10000000
pcre.recursion_limit = 500000
```

---

## Testing Checklist

1. Open `https://system.khbevents.com/booths?view=canvas&floor_plan_id=4`.
2. Confirm sidebar on left, canvas on right.
3. Hard refresh (Ctrl+Shift+R) to verify cache busting.
4. Test: booth drag/drop, save positions, zone expand/collapse.
5. Check responsive layout on smaller viewports.

---

## Related Documentation

- [CANVAS_PERMISSIONS_GUIDE.md](CANVAS_PERMISSIONS_GUIDE.md) — Who can edit vs view.
- [SYSTEM-KHBEVENTS-DOCUMENTATION.md](../SYSTEM-KHBEVENTS-DOCUMENTATION.md) — Canvas performance, routes, resumption.
