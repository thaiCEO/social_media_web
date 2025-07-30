<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllPostsCollection;
use App\Http\Resources\FriendResource;
use App\Models\Friendship;
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

        $posts = Post::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();

        return Inertia::render('User/HomePage', [
            'posts' => new AllPostsCollection($posts),
        ]);
        
    }

    // public function show(int $id)
    // {
        
    //     $posts = Post::where('user_id', $id)->orderBy('created_at', 'desc')->get();
    //     return Inertia::render('User/ProfileAccount', [
    //         'user' => User::find($id),
    //         'posts' => new AllPostsCollection($posts)
    //     ]);
    // }

    
// public function show(int $id)
// {
//     $user = User::findOrFail($id);
//     $authId = Auth::id();

//     $posts = Post::where('user_id', $id)->orderBy('created_at', 'desc')->get();

//     // Get friendship status between authenticated user and profile user
//     $friendship = Friendship::where(function ($q) use ($authId, $id) {
//         $q->where('sender_id', $authId)->where('receiver_id', $id);
//     })->orWhere(function ($q) use ($authId, $id) {
//         $q->where('sender_id', $id)->where('receiver_id', $authId);
//     })->first();

//     $friendshipStatus = 'not_friends';
//     if ($friendship) {
//         if ($friendship->status === 'pending') {
//             $friendshipStatus = $friendship->sender_id === $authId ? 'pending_sent' : 'pending_received';
//         } elseif ($friendship->status === 'accepted') {
//             $friendshipStatus = 'friends';
//         }
//     }

  

//       // Get confirmed friends (status = accepted)
//     $friends = \App\Models\Friendship::where(function ($q) use ($id) {
//             $q->where('sender_id', $id)
//               ->orWhere('receiver_id', $id);
//         })
//         ->where('status', 'accepted')
//         ->get()
//         ->map(function ($friendship) use ($id) {
//             // Determine which user is the friend (not the profile owner)
//             $friendId = $friendship->sender_id == $id ? $friendship->receiver_id : $friendship->sender_id;
//             return \App\Models\User::select('id', 'name', 'image')->find($friendId);
//         });


//     return Inertia::render('User/ProfileAccount', [
//         'user' => User::find($id),
//         'posts' => new AllPostsCollection($posts),
//         'friendshipStatus' => $friendshipStatus,
//         'friends' => $friends,
        
//     ]);
// }


public function show(int $id)
{
    $user = User::findOrFail($id);
    $authId = Auth::id();

    // Determine friendship status between authenticated user and profile user
    $friendshipStatus = 'not_friends';

    if ($authId) {
        $friendship = Friendship::where(function ($q) use ($authId, $id) {
                $q->where('sender_id', $authId)
                  ->where('receiver_id', $id);
            })
            ->orWhere(function ($q) use ($authId, $id) {
                $q->where('sender_id', $id)
                  ->where('receiver_id', $authId);
            })
            ->first();

        if ($friendship) {
            if ($friendship->status === 'pending') {
                $friendshipStatus = $friendship->sender_id === $authId
                    ? 'pending_sent'
                    : 'pending_received';
            } elseif ($friendship->status === 'accepted') {
                $friendshipStatus = 'friends';
            }
        }
    }

    // Build posts query with visibility rules
    $postsQuery = Post::with(['user', 'likes', 'comments.user'])
        ->where('user_id', $id)
        ->orderBy('created_at', 'desc');

    if (!$authId) {
        // Guest can only see public posts
        $postsQuery->where('visibility', 'public');
    } else {
        // If not friends and not same user -> restrict to public
        if ($authId !== $id && $friendshipStatus !== 'friends') {
            $postsQuery->where('visibility', 'public');
        }
        // If friends or same user -> can see both public and friends posts
    }

    $posts = $postsQuery->get();

    // Attach friendshipStatus to each post (for Vue)
    $posts->transform(function ($post) use ($authId, $friendshipStatus) {
        $post->friendshipStatus = $post->user_id === $authId ? 'friends' : $friendshipStatus;
        return $post;
    });

    // Get confirmed friends (status = accepted)
    $friends = Friendship::where(function ($q) use ($id) {
            $q->where('sender_id', $id)
              ->orWhere('receiver_id', $id);
        })
        ->where('status', 'accepted')
        ->get()
        ->map(function ($friendship) use ($id) {
            $friendId = $friendship->sender_id == $id
                ? $friendship->receiver_id
                : $friendship->sender_id;
            return User::select('id', 'name', 'image')->find($friendId);
        });


        
    return Inertia::render('User/ProfileAccount', [
        'user' => $user,
        'posts' => new AllPostsCollection($posts),
        'friendshipStatus' => $friendshipStatus,
        'friends' => $friends,
    ]);
}







    public function updateImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

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

       $user = Auth::user();

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
