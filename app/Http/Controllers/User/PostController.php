<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllPostsCollection;
use App\Models\Post;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Inertia\Inertia;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
 public function index()
    {
        $authUser = auth()->user();

        $query = Post::with([
            'user',
            'likes',
            'comments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->with(['user', 'replies.user', 'replies.replies.user']);
                }
            ])->orderBy('created_at', 'desc');

        if ($authUser) {
            $authUser->load(['sentFriends', 'receivedFriends']);
            $friends = $authUser->sentFriends->merge($authUser->receivedFriends);
            $friendIds = $friends->pluck('id')->unique();

            $query->where(function ($q) use ($authUser, $friendIds) {
                $q->where('visibility', 'public')
                  ->orWhere(function ($q2) use ($authUser, $friendIds) {
                      $q2->where('visibility', 'friends')
                          ->whereIn('user_id', $friendIds)
                          ->orWhere('user_id', $authUser->id);
                  });
            });
        } else {
            $query->where('visibility', 'public');
            $friends = collect();
        }

        $posts = $query->get();

        return Inertia::render('User/HomePage', [
            'posts' => new AllPostsCollection($posts),
            'authUser' => $authUser,
            'friends' => $friends->map(fn($friend) => [
                'id' => $friend->id,
                'name' => $friend->name,
                'image' => $friend->image
                    ? '/storage/' . $friend->image
                    : 'https://i.pinimg.com/736x/15/0f/a8/150fa8800b0a0d5633abc1d1c4db3d87.jpg',
            ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

public function store(Request $request)
{
    $request->validate([
        'text' => 'nullable|string',
        'images' => 'nullable|array',
        'images.*' => 'image|max:10240',
        'visibility' => 'required|in:public,friends',
    ]);

    if (!$request->text && !$request->hasFile('images')) {
        return back()->withErrors(['text' => 'Post must contain text or image.']);
    }

    // Save post first
    $post = Post::create([
        'user_id' => auth()->id(),
        'text' => $request->text,
        'visibility' => $request->visibility,
    ]);

    // Attach images after post is saved
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('posts', 'public');
            $post->images()->create(['image' => $path]);
        }
    }

    return back()->with('success', 'Post created successfully.');
}



public function showImage(Post $post, $index)
{
    $post->load(['user', 'images']);
    return inertia('User/PostPreview', [
        'post' => $post,
        'index' => (int)$index,
    ]);
}



public function destroy(int $id)
{
    $post = Post::with('images')->findOrFail($id);

    if ($post->user_id !== auth()->id()) {
        abort(403, 'Unauthorized action.');
    }

    // Delete all images
    foreach ($post->images as $image) {
        $imagePath = public_path('storage/' . $image->image);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $image->delete();
    }

    $post->delete();

    return redirect()->back()->with('success', 'Post deleted successfully.');
}





}
