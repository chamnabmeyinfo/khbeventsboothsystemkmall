# Changelog

All notable changes to the KHB Events Booth Booking System are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added
- Version tracking feature: backend system versions and changelog (admin).
- Documentation page: in-app Documentation & Changelog view for authenticated users.
- `system_versions` table and `SystemVersion` model for release history.
- Config `app.version` (env `APP_VERSION`, default `1.0.0`).
- Version management UI: list, create, show, set current (admin only).
- Sidebar links: Documentation (all users), Versions (admin).

## [1.0.0] - 2026-02-10

### Added
- Initial release baseline.
- Version and changelog documentation structure.
