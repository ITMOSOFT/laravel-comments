<?php

namespace ItmoSoft\Comments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use ItmoSoft\Comments\Exceptions\CommentException;
use ItmoSoft\Comments\Models\Comment;

class CommentManager
{
    public function create(
        Model $commentable,
        Model $author,
        string $body,
        ?int $parentId = null,
        string $visibility = 'public',
        array $metadata = [],
    ): Comment {
        $this->assertBody($body);
        $this->assertVisibility($visibility);

        return DB::transaction(function () use ($commentable, $author, $body, $parentId, $visibility, $metadata) {
            $depth = 0;

            if ($parentId !== null) {
                $parent = Comment::query()->whereKey($parentId)->lockForUpdate()->first();

                if (! $parent
                    || $parent->commentable_type !== $commentable->getMorphClass()
                    || (string) $parent->commentable_id !== (string) $commentable->getKey()) {
                    throw new CommentException('The parent comment does not belong to this subject.');
                }

                $depth = $this->depthOf($parent) + 1;
                $maxDepth = (int) config('itmosoft-comments.max_depth', 5);

                if ($depth > $maxDepth) {
                    throw new CommentException('The maximum comment thread depth has been reached.');
                }
            }

            return Comment::query()->create([
                'commentable_type' => $commentable->getMorphClass(),
                'commentable_id' => $commentable->getKey(),
                'author_type' => $author->getMorphClass(),
                'author_id' => $author->getKey(),
                'parent_id' => $parentId,
                'body' => trim($body),
                'visibility' => $visibility,
                'metadata' => $metadata,
                'is_edited' => false,
            ]);
        });
    }

    public function update(Comment $comment, Model $author, string $body): Comment
    {
        $this->assertBody($body);

        if (! $this->isAuthor($comment, $author)) {
            throw new CommentException('Only the comment author may edit this comment.');
        }

        $comment->forceFill([
            'body' => trim($body),
            'is_edited' => true,
            'edited_at' => now(),
        ])->save();

        return $comment->refresh();
    }

    public function delete(Comment $comment, Model $author): void
    {
        if (! $this->isAuthor($comment, $author)) {
            throw new CommentException('Only the comment author may delete this comment.');
        }

        $comment->delete();
    }

    private function depthOf(Comment $comment): int
    {
        $depth = 0;
        $seen = [];
        $parentId = $comment->parent_id;

        while ($parentId !== null) {
            if (isset($seen[$parentId])) {
                throw new CommentException('A circular comment parent relationship was detected.');
            }

            $seen[$parentId] = true;
            $parent = Comment::query()->find($parentId);

            if (! $parent) {
                break;
            }

            $depth++;
            $parentId = $parent->parent_id;
        }

        return $depth;
    }

    private function isAuthor(Comment $comment, Model $author): bool
    {
        return $comment->author_type === $author->getMorphClass()
            && (string) $comment->author_id === (string) $author->getKey();
    }

    private function assertBody(string $body): void
    {
        if (trim($body) === '') {
            throw new CommentException('A comment body cannot be empty.');
        }
    }

    private function assertVisibility(string $visibility): void
    {
        $allowed = config('itmosoft-comments.visibilities', ['public', 'internal']);

        if (! in_array($visibility, $allowed, true)) {
            throw new CommentException('The selected comment visibility is not supported.');
        }
    }
}
