# Release checklist

1. Run `composer install`.
2. Run `composer test` and ensure all tests pass.
3. Review package contents and `CHANGELOG.md`.
4. Commit the source to `main`.
5. Create and push the `v1.0.0` tag.
6. Create a GitHub Release with the title and notes below.
7. Submit the repository URL to Packagist.

## GitHub Release title

`v1.0.0 - Initial Release`

## GitHub Release notes

### Highlights
- Polymorphic comments for Eloquent models.
- Threaded replies with configurable depth.
- Public/internal visibility labels.
- Author-checked editing and soft deletion.
- Model trait and query scopes.

### Installation
```bash
composer require itmosoft/laravel-comments
php artisan vendor:publish --tag=itmosoft-comments-config
php artisan migrate
```

### Requirements
- PHP 8.3+
- Laravel 12 or 13

### Limitations
No mentions, notifications, reactions, attachments, moderation queue, spam detection, or UI.
