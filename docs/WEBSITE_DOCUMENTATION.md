# Website Documentation

**KHB Events Booth Booking System**  
**Last updated:** February 10, 2026

---

## Overview

This document describes the **in-app documentation and version tracking** available on the website (application).

---

## Documentation page

- **URL (authenticated):** `/documentation`
- **Route name:** `documentation.index`
- **Access:** Any authenticated user.

The Documentation page shows:

1. **Current version** – Application version (from config and, if set, the “current” system version in the database), release date, summary, and changelog.
2. **Changelog** – Full list of all recorded system versions (newest first) with release date, summary, and changelog text.
3. **Website documentation** – Short note that project docs live in the `docs/` folder and that `CHANGELOG.md` is in the project root. Admins see a link to manage versions.

---

## Update Changelog (admin) – single page for all changelog updates

- **URL (admin):** `/changelog/update`
- **Route name:** `changelog.update`
- **Access:** Admin only.

Use this page whenever you add a new feature or change and want to record it:

1. **Quick add changelog entry** – Enter a line (e.g. “Added report export”) and click “Add to changelog”. It appends to the current version. If no current version exists, one is created.
2. **Edit current version** – Change the summary and full changelog text of the current version, then click “Save changes”.
3. **New release** – Use “Create version” to add a new version (number, date, changelog) and optionally set it as current.
4. **Recent versions** – See the latest versions and open “View” or go to “All” for the full list.

---

## Version list & create (admin)

- **URL (admin):** `/versions`
- **Route name:** `versions.index`
- **Access:** Admin only.

Admins can:

- **List versions** – View all system versions (paginated).
- **Add version** – Create a new release record (version number, release date, summary, changelog, optional “current” flag).
- **View version** – See details of a single version.
- **Set current** – Mark one version as the current/live release (used on the Documentation page).

### Version data

- **Version** – String (e.g. `1.0.0`), unique.
- **Released at** – Date.
- **Summary** – Short description (optional).
- **Changelog** – Free-text changelog for that version (optional).
- **Is current** – Boolean; only one version should be current at a time.

---

## Changelog (repository)

- **File:** `CHANGELOG.md` in the project root.
- **Purpose:** Human-readable release history for the codebase (Keep a Changelog style).
- **Usage:** Update when releasing; can mirror or summarize what is stored in the app’s Version feature.

---

## Config

- **App version:** `config('app.version')` (default `1.0.0`).
- **Env:** `APP_VERSION` (optional). Example: `APP_VERSION=1.2.0`.

---

## Navigation

- **Documentation** – In sidebar (Quick Access) and in mobile nav for all authenticated users.
- **Versions** – In sidebar under **System Administration** for admins only.

---

## Related docs

- [docs/README.md](README.md) – Documentation index.
- [docs/03-development/REFACTORING_PROGRESS.md](03-development/REFACTORING_PROGRESS.md) – Refactoring history.
- [CHANGELOG.md](../CHANGELOG.md) – Repository changelog.
