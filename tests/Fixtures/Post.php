<?php

namespace ItmoSoft\Comments\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use ItmoSoft\Comments\Concerns\HasComments;

class Post extends Model
{
    use HasComments;

    protected $table = 'posts';
    protected $guarded = [];
    public $timestamps = false;
}
