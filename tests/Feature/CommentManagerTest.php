<?php

namespace ItmoSoft\Comments\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ItmoSoft\Comments\CommentManager;
use ItmoSoft\Comments\Exceptions\CommentException;
use ItmoSoft\Comments\Tests\Fixtures\Post;
use ItmoSoft\Comments\Tests\Fixtures\User;
use ItmoSoft\Comments\Tests\TestCase;

class CommentManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        });

        $this->artisan('migrate', ['--force' => true]);
    }

    public function test_it_creates_a_comment_for_an_eloquent_subject(): void
    {
        [$post, $user] = $this->records();
        $comment = app(CommentManager::class)->create($post, $user, '  Hello world  ');

        $this->assertSame('Hello world', $comment->body);
        $this->assertSame('public', $comment->visibility);
        $this->assertSame($post->getKey(), $comment->commentable_id);
    }

    public function test_it_creates_threaded_replies(): void
    {
        [$post, $user] = $this->records();
        $manager = app(CommentManager::class);
        $parent = $manager->create($post, $user, 'Parent');
        $reply = $manager->create($post, $user, 'Reply', $parent->id);

        $this->assertSame($parent->id, $reply->parent_id);
        $this->assertCount(1, $parent->replies);
    }

    public function test_it_rejects_a_parent_from_another_subject(): void
    {
        [$post, $user] = $this->records();
        $other = Post::query()->create(['title' => 'Other']);
        $parent = app(CommentManager::class)->create($other, $user, 'Other subject');

        $this->expectException(CommentException::class);
        app(CommentManager::class)->create($post, $user, 'Invalid reply', $parent->id);
    }

    public function test_it_rejects_empty_comment_bodies(): void
    {
        [$post, $user] = $this->records();

        $this->expectException(CommentException::class);
        app(CommentManager::class)->create($post, $user, '   ');
    }

    public function test_it_supports_internal_visibility(): void
    {
        [$post, $user] = $this->records();
        $comment = app(CommentManager::class)->create($post, $user, 'Staff only', null, 'internal');

        $this->assertSame('internal', $comment->visibility);
    }

    public function test_it_rejects_unknown_visibility(): void
    {
        [$post, $user] = $this->records();

        $this->expectException(CommentException::class);
        app(CommentManager::class)->create($post, $user, 'Nope', null, 'secret');
    }

    public function test_author_can_edit_a_comment(): void
    {
        [$post, $user] = $this->records();
        $manager = app(CommentManager::class);
        $comment = $manager->create($post, $user, 'Before');
        $comment = $manager->update($comment, $user, 'After');

        $this->assertSame('After', $comment->body);
        $this->assertTrue($comment->is_edited);
        $this->assertNotNull($comment->edited_at);
    }

    public function test_non_author_cannot_edit_or_delete_a_comment(): void
    {
        [$post, $user, $other] = $this->records();
        $manager = app(CommentManager::class);
        $comment = $manager->create($post, $user, 'Owned comment');

        try {
            $manager->update($comment, $other, 'Changed');
            $this->fail('Expected author check to fail.');
        } catch (CommentException) {
            $this->assertSame('Owned comment', $comment->fresh()->body);
        }

        $this->expectException(CommentException::class);
        $manager->delete($comment, $other);
    }

    public function test_author_can_soft_delete_a_comment(): void
    {
        [$post, $user] = $this->records();
        $manager = app(CommentManager::class);
        $comment = $manager->create($post, $user, 'Remove me');

        $manager->delete($comment, $user);

        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }

    private function records(): array
    {
        $user = User::query()->create(['name' => 'Commenter']);
        $other = User::query()->create(['name' => 'Other']);
        $post = Post::query()->create(['title' => 'Example']);

        return [$post, $user, $other];
    }
}
