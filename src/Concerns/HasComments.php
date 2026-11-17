<?php

namespace ItmoSoft\Comments\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use ItmoSoft\Comments\Models\Comment;

trait HasComments
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    public function allComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
