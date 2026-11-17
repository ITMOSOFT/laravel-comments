# Laravel Comments

Polymorphic comments and threaded discussions for Laravel Eloquent models.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

## Installation

```bash
composer require itmosoft/laravel-comments
php artisan vendor:publish --tag=itmosoft-comments-config
php artisan migrate
```

## Usage

Add the trait to any Eloquent model:

```php
use ItmoSoft\Comments\Concerns\HasComments;

class Post extends Model
{
    use HasComments;
}
```

Create a comment or reply:

```php
use ItmoSoft\Comments\CommentManager;

$manager = app(CommentManager::class);

$comment = $manager->create(
    commentable: $post,
    author: auth()->user(),
    body: 'This looks good.',
);

$reply = $manager->create(
    commentable: $post,
    author: auth()->user(),
    body: 'Thanks!',
    parentId: $comment->id,
);
```

Edit or delete (only the original author is accepted by the manager):

```php
$manager->update($comment, auth()->user(), 'Updated comment.');
$manager->delete($comment, auth()->user());
```

Query top-level comments and replies:

```php
$post->comments()->with(['author', 'replies.author'])->latest()->get();
$post->allComments()->public()->get();
$post->allComments()->internal()->get();
```

Metadata is stored as JSON and exposed as a PHP array on the comment model.

## Visibility

Supported values are `public` and `internal`. Visibility is stored as data; your application
must enforce who can read internal comments. Do not expose internal comments in public APIs.

## Configuration

Publish `itmosoft-comments.php` to configure the table name, maximum thread depth, and allowed
visibility values. The default maximum thread depth is 5 (a root comment has depth 0).

## Responsibilities and limitations

- The package checks comment authorship for edit and delete operations.
- Your application must implement authorization for creating comments and reading internal comments.
- This release does not include mentions, notifications, reactions, attachments, moderation queues,
  spam detection, UI, or automatic model observers.
- Soft-deleting a parent comment leaves replies intact; the parent foreign key is configured to null
  if a row is physically deleted.

## Testing

```bash
composer install
composer test
```

## License

MIT. See [LICENSE](LICENSE).
