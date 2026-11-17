<?php

namespace ItmoSoft\Comments;

use Illuminate\Support\ServiceProvider;

class CommentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/comments.php', 'itmosoft-comments');
        $this->app->singleton(CommentManager::class);
        $this->app->alias(CommentManager::class, 'itmosoft.comments');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/comments.php' => config_path('itmosoft-comments.php'),
        ], 'itmosoft-comments-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
