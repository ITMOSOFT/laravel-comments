# Release checklist

1. Run `composer install`.
2. Run `composer test` and ensure all tests pass.
3. Review package contents and `CHANGELOG.md`.
4. Commit the source to `main`.
5. Create and push the `v1.0.1` tag only after tests and CI pass.
6. Create a GitHub Release with the title and notes below.
7. Submit the GitHub repository URL to Packagist.

## GitHub Release title

`v1.0.1 - Fix JSON Metadata Persistence`

## GitHub Release notes

### Fixed
- Added the Eloquent `array` cast for comment metadata.
- Fixed array-to-string conversion errors when creating comments with metadata.

### Installation
```bash
composer require itmosoft/laravel-comments:^1.0
php artisan vendor:publish --tag=itmosoft-comments-config
php artisan migrate
```

### Requirements
- PHP 8.3+
- Laravel 12 or 13
