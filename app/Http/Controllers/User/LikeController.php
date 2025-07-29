<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
     public function store(Post $post)
    {
        $userId = auth()->id();

        // Create or ignore if already liked
        $post->likes()->updateOrCreate([
            'user_id' => $userId,
        ]);

        return back()->with('success', 'Post liked');
    }

    public function destroy(Post $post)
    {
        $userId = auth()->id();

        $post->likes()->where('user_id', $userId)->delete();

        return back()->with('success', 'Like removed');
    }

    
}