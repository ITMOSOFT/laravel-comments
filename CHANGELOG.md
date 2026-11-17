# Changelog

## [1.0.1] - 2026-11-17

### Fixed
- Added the array cast for comment metadata so Laravel serializes metadata as JSON during persistence.

## [1.0.0] - 2026-11-17

### Added
- Polymorphic comments for Eloquent models.
- Threaded replies with configurable maximum depth.
- Public and internal visibility labels.
- Author-attributed comments with edit and soft-delete operations.
- JSON metadata support.
- `HasComments` model trait.
- Config publishing, migrations, PHPUnit tests, and GitHub Actions CI.
