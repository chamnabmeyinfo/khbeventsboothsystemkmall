# Changelog

All notable changes to the KHB Events Booth Booking System are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added
- Version tracking feature: backend system versions and changelog (admin).

## [1.1.0] - 2026-03-07

### Added
- **Floor Plan Designer UI Redesign**: Side-by-side layout (20% sidebar / 80% canvas) in `designer-main-row`.
- **Asset cache busting**: `config('app.asset_version')` and `ASSET_VERSION` env for CSS/JS reload during development.
- **Photoshop-style design tokens**: CSS variables (`--fpd-*`) for consistent theming in floor plan designer.

### Changed
- **Designer layout**: `sidebar-content` (Booth Numbers) on left, `canvas-container` (floor plan image) on right; responsive grid.
- **CSS structure**: `public/css/floor-plan-designer.css` — grid layout, flexbox sections, zone/booth styling.
- **Blade template**: `resources/views/booths/index.blade.php` — cache-busted CSS link with `filemtime()` fallback.

### Fixed
- **Production 500 after deploy**: Bootstrap cache (`config.php`, `packages.php`, `services.php`) was committed from dev; production uses `composer install --no-dev` (no Ignition). Fix: delete bootstrap cache on server so Laravel regenerates for production.
- **PCRE limits for large Blade**: `public/.user.ini` — `pcre.backtrack_limit` and `pcre.recursion_limit` for large booth index view.

### Documentation
- `docs/05-guides/FLOOR_PLAN_DESIGNER_UI.md` — designer layout and styling guide.
- `docs/SYSTEM-KHBEVENTS-DOCUMENTATION.md` — updated with designer layout section.
- Documentation page: in-app Documentation & Changelog view for authenticated users.
- `system_versions` table and `SystemVersion` model for release history.
- Config `app.version` (env `APP_VERSION`, default `1.0.0`).
- Version management UI: list, create, show, set current (admin only).
- Sidebar links: Documentation (all users), Versions (admin).

## [1.0.0] - 2026-02-10

### Added
- Initial release baseline.
- Version and changelog documentation structure.
