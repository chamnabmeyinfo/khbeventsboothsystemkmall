# system.khbevents.com — System Documentation

This document describes the **system.khbevents.com** Laravel application (KHBEvents booth/floor plan management) and includes references for resuming work in the future.

---

## 1. Project overview

- **Application:** Laravel (PHP 8.1+; production uses PHP 8.2/8.3 via cPanel EA).
- **Purpose:** Booth and floor plan management: canvas designer, management table, bookings, clients, zones, and public floor plan views.
- **Key URL:** `https://system.khbevents.com`
- **Canvas view:** `https://system.khbevents.com/booths?view=canvas&floor_plan_id=<id>`

---

## 2. Chat / conversation ID (for resumption)

Use this ID to resume or reference this project in Cursor or support channels:

| Item | Value |
|------|--------|
| **Conversation/chat ID** | `ef13cded-3b5c-4207-ac91-aad74eff91d9` |
| **Transcript path** | `.cursor/projects/root/agent-transcripts/ef13cded-3b5c-4207-ac91-aad74eff91d9.txt` |

*(Update the row above if you use a different chat or thread ID.)*

---

## 3. Codebase location and layout

- **App root:** `/home/khbevents/system.khbevents.com` (or project root under your workspace).
- **Key areas:**
  - `app/Http/Controllers/BoothController.php` — booths index, canvas, canvas data API, management table.
  - `app/Http/Controllers/FloorPlanController.php` — floor plan CRUD, image upload.
  - `resources/views/booths/` — canvas view `index.blade.php`, management view, public view, partials.
  - `routes/web.php` — all web routes; booth canvas data route: `booths.canvas-data`.

---

## 4. Canvas page performance (what was done)

### 4.1 Problem

- High load on `booths?view=canvas&floor_plan_id=`: many queries, large HTML (big inline booth JSON).

### 4.2 Phase 1 — Backend and payload reduction

- Removed six full-table “map” queries (reserveMap, companyMap, categoryMap, subCategoryMap, assetMap, boothTypeMap) from canvas index; kept empty arrays for compatibility.
- Optimized booth query: `Booth::select([...])` with limited columns and `with(['client:id,company,name', ...])`.
- Trimmed FloorPlan, Category, Asset, BoothType, Client queries to needed columns only.
- Removed duplicate booth JSON in Blade; single `window.boothsData` usage.
- Removed unused `companyMap` fallback in JS.
- **Result:** Fewer queries (e.g. 37→24), smaller HTML (~1.58MB→~1.42MB).

### 4.3 Phase 2 — Async booth data

- **New endpoint:** `GET /booths/canvas/data?floor_plan_id=<id>`  
  - Route name: `booths.canvas-data`  
  - Controller: `BoothController@canvasData`  
  - Returns: `{ success, floor_plan_id, count, booths }`.
- **Controller split:**
  - Initial page load uses `getCanvasSidebarBoothsForFloorPlan($floorPlanId)` — lightweight booth list for sidebar/stats.
  - Full booth payload (positions, styling) only from `canvasData()` via `getCanvasBoothsForFloorPlan()` and `mapCanvasBoothsForJs()`.
- **Front-end:**
  - `FloorPlanDesigner.loadCanvasBoothsData(forceReload)` — single-flight fetch to canvas data URL, writes `window.boothsData`.
  - On failure, falls back to `window.boothsData = []` (no crash).
  - Canvas init runs after `Promise.all([..., this.loadCanvasBoothsData()])`; then `loadSavedPositions()` uses `window.boothsData`.
- **Result:** Initial HTML no longer embeds large booth JSON (~1.26MB); booth data loaded asynchronously (~200KB JSON, 2 queries). Safe fallback if request fails.

---

## 5. Important routes and endpoints

| Route / URL | Method | Purpose |
|-------------|--------|---------|
| `/booths` | GET | Booths index; `view=table` → management table, `view=canvas` → canvas. |
| `/booths?view=canvas&floor_plan_id=<id>` | GET | Canvas view for a floor plan. |
| `/booths/canvas/data` | GET | JSON booth data for canvas; query: `floor_plan_id`. |
| `/floor-plans` | GET | Floor plan list. |
| `/floor-plans/{id}/public` | GET | Public floor plan view (no auth). |

---

## 6. Environment and config

- **Env:** `.env` (e.g. `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, DB credentials).
- **PHP:** cPanel EA PHP 8.2/8.3; `php_fileinfo` required for image upload validation (installed; fallback to extension-based validation in code if missing).
- **Auth:** User model uses `user` table, auth identifier `username`; be aware of `auth()->id()` vs numeric `user_id` in DB when touching activity_logs/notifications.

---

## 7. Known follow-ups (from earlier audits)

- Auth identifier mismatch: `auth()->id()` can return username (string) vs integer `user_id` in some tables.
- Missing core config files in repo: `filesystems`, `mail`, `cache`, `queue` (if used).
- Booking timeline: `BookingService` / `BookingTimeline` — ensure `booth_id` and `details` (not `description`) are set.
- Scheduler: ensure `php artisan schedule:run` is in cron for the app user.
- Session cookie: consider `SESSION_SECURE_COOKIE=true` in production.
- Dependency advisories: run `composer audit` and plan upgrades.

---

## 8. How to test canvas after changes

1. Open `https://system.khbevents.com/booths?view=canvas&floor_plan_id=4` (or your floor plan ID).
2. Hard refresh (Ctrl+Shift+R).
3. In DevTools Network: confirm document load and `GET /booths/canvas/data?floor_plan_id=...` returns 200.
4. Check: booths render, sidebar counts, drag/drop, save positions, refresh and positions persist.
5. Optional: block `/booths/canvas/data` in DevTools; page should not throw (fallback to empty booth list).

---

## 9. Floor Plan Designer UI (March 2026)

### Layout redesign

- **Side-by-side:** `designer-main-row` uses CSS Grid: 20% sidebar (left), 80% canvas (right).
- **Sidebar:** Booth numbers by zone, floor plan selector, stats.
- **Canvas:** Floor plan image, draggable booths, zoom/pan.
- **Files:** `public/css/floor-plan-designer.css`, `resources/views/booths/index.blade.php`.

### Cache busting

- `config/app.php`: `asset_version` (env `ASSET_VERSION`).
- Blade: CSS link `?v={{ config('app.asset_version') ?? filemtime(...) }}`.
- Set `ASSET_VERSION=2` in `.env` during development to force reload.

### Production deploy notes

- **500 after push:** Delete `bootstrap/cache/config.php`, `packages.php`, `services.php` on server if "IgnitionServiceProvider not found" — Laravel regenerates for production.
- **Large Blade:** `public/.user.ini` may need `pcre.backtrack_limit` / `pcre.recursion_limit` for booth index view.

See `docs/05-guides/FLOOR_PLAN_DESIGNER_UI.md` for full guide.

---

## 10. Resuming work later

- Search this repo for “canvas”, “booths.canvas-data”, “loadCanvasBoothsData”, “getCanvasBoothsForFloorPlan”.
- Use the **Conversation/chat ID** above to reopen the Cursor conversation or transcript.
- Re-read this file and `app/Http/Controllers/BoothController.php` (index, canvasData, getCanvasSidebarBoothsForFloorPlan, getCanvasBoothsForFloorPlan, mapCanvasBoothsForJs) for current behavior.
- Designer UI: `docs/05-guides/FLOOR_PLAN_DESIGNER_UI.md`, `public/css/floor-plan-designer.css`.

---

*Last updated: March 7, 2026 — canvas performance, async booth data, designer layout redesign.*
