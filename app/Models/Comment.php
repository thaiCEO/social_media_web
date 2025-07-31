<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  // Add this
        'post_id',
        'parent_id',
        'text',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }


     public function replies()
    {
         return $this->hasMany(Comment::class, 'parent_id')
          ->with('user', 'replies'); // recursive
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }
    
}
