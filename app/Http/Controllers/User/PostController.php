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
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'visibility' => 'required|in:public,friends',
    ]);

    if (!$request->text && !$request->hasFile('image')) {
        return back()->withErrors(['text' => 'Post must contain text or image.']);
    }

    $post = new Post();
    $post->user_id = auth()->id();
    $post->text = $request->text;
    $post->visibility = $request->visibility;

    if ($request->hasFile('image')) {
        // Just store the file without resizing or UUID
        $path = $request->file('image')->store('posts', 'public');

        // Save relative path in DB
        $post->image = $path;
    }

    $post->save();

    return back()->with('success', 'Post created successfully.');
}


public function destroy(int $id)
{
    $post = Post::findOrFail($id);

    // Ensure the logged-in user owns this post
    if ($post->user_id !== auth()->id()) {
        abort(403, 'Unauthorized action.');
    }

    // Delete image if it exists
    if (!empty($post->image)) {
        $imagePath = public_path('storage/' . $post->image);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $post->delete();

    return redirect()->back()->with('success', 'Post deleted successfully.');
}




}
