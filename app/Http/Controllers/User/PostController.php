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
    public function index() {
        
        // Fetch all posts with relationships
        $posts = Post::with(['user', 'likes', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('User/HomePage', [
            'posts' => new AllPostsCollection($posts),
            'authUser' => auth()->user(), // this will be null for guests
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
    ]);

    if (!$request->text && !$request->hasFile('image')) {
        return back()->withErrors(['text' => 'Post must contain text or image.']);
    }

    $post = new Post();
    $post->user_id = auth()->id();
    $post->text = $request->text;

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
