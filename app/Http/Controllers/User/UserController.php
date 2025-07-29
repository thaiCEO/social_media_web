<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllPostsCollection;
use App\Models\Post;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UserController extends Controller
{
  public function index()
    {
        $posts = Post::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        return Inertia::render('User/HomePage', [
            'posts' => new AllPostsCollection($posts)
        ]);
    }

    public function show(int $id)
    {
        
        $posts = Post::where('user_id', $id)->orderBy('created_at', 'desc')->get();
        return Inertia::render('User/ProfileAccount', [
            'user' => User::find($id),
            'posts' => new AllPostsCollection($posts)
        ]);
    }


    public function updateImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();

        // Delete old image if exists
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        // Store new image
        $path = $request->file('image')->store('profile', 'public');

        // Update user image path
        $user->image = $path;
        $user->save();

        return redirect()->back()->with('success', 'Profile image updated successfully.');
    }


   public function updateCoverImage(Request $request)
    {
        $request->validate([
            'cover_image' => 'required|image|max:2048', // validate image max 2MB
            'height' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'left' => 'nullable|numeric',
            'top' => 'nullable|numeric',
        ]);

       $user = auth()->user();

     if ($request->hasFile('cover_image')) {
        // Delete old cover image file if exists
        if ($user->cover_image && Storage::disk('public')->exists($user->cover_image)) {
            Storage::disk('public')->delete($user->cover_image);
        }

        // Store new uploaded image in 'public/cover_photo' folder
        $path = $request->file('cover_image')->store('cover_photo', 'public');

        // Save new path to user model
        $user->cover_image = $path;
        $user->save();

        return redirect()->back()->with('success', 'Cover image updated successfully');
    }

        return redirect()->back()->withErrors(['cover_image' => 'Please upload a valid image']);
    }
   
}
