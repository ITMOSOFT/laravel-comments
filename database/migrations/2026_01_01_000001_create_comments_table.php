<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('itmosoft-comments.table', 'comments'), function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable');
            $table->morphs('author');
            $table->foreignId('parent_id')->nullable()
                ->constrained(config('itmosoft-comments.table', 'comments'))
                ->nullOnDelete();
            $table->text('body');
            $table->string('visibility', 20)->default('public')->index();
            $table->json('metadata')->nullable();
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['commentable_type', 'commentable_id', 'created_at'], 'comments_subject_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('itmosoft-comments.table', 'comments'));
    }
};
