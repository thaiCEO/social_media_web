<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Friendship;
use App\Models\User;
use FriendRequestSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FriendshipController extends Controller
{



   public function sendRequest($receiverId)
{
    $senderId = Auth::id();

    if ($senderId == $receiverId) {
        return back()->withErrors('You cannot friend yourself.');
    }

    // Find existing friendship in any direction
    $friendship = Friendship::where(function ($q) use ($senderId, $receiverId) {
        $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
    })->orWhere(function ($q) use ($senderId, $receiverId) {
        $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
    })->first();

    if ($friendship) {
        if ($friendship->status === 'declined') {
            // Delete declined request and allow re-send
            $friendship->delete();
        } elseif ($friendship->status === 'accepted') {
            return back()->withErrors('You are already friends.');
        } elseif ($friendship->status === 'pending') {
            return back()->withErrors('Friend request already sent.');
        }
    }

    // Create a new friend request
    Friendship::create([
        'sender_id' => $senderId,
        'receiver_id' => $receiverId,
        'status' => 'pending',
    ]);

    return back()->with('success', 'Friend request sent!');
}




    public function cancelRequest($receiverId)
    {
        $senderId = Auth::id();

        $friendRequest = Friendship::where('sender_id', $senderId)
                        ->where('receiver_id', $receiverId)
                        ->where('status', 'pending')
                        ->first();

        if ($friendRequest) {
            $friendRequest->delete();
            return back()->with('success', 'Friend request canceled.');
        }

        return back()->withErrors('Friend request not found.');
    }

    public function acceptRequest($senderId)
    {
        $receiverId = Auth::id();

        $friendRequest = Friendship::where('sender_id', $senderId)
                        ->where('receiver_id', $receiverId)
                        ->where('status', 'pending')
                        ->first();

        if ($friendRequest) {
            $friendRequest->status = 'accepted';
            $friendRequest->save();
            return back()->with('success', 'Friend request accepted.');
        }

        return back()->withErrors('Friend request not found.');
    }

    public function declineRequest($senderId)
    {
        $receiverId = Auth::id();

        $friendRequest = Friendship::where('sender_id', $senderId)
                        ->where('receiver_id', $receiverId)
                        ->where('status', 'pending')
                        ->first();

        if ($friendRequest) {
            $friendRequest->status = 'declined';
            $friendRequest->save();
            return back()->with('success', 'Friend request declined.');
        }

        return back()->withErrors('Friend request not found.');
    }

    public function removeFriend($friendId)
    {
        $userId = Auth::id();

        $friendship = Friendship::where(function ($q) use ($userId, $friendId) {
            $q->where('sender_id', $userId)->where('receiver_id', $friendId);
        })->orWhere(function ($q) use ($userId, $friendId) {
            $q->where('sender_id', $friendId)->where('receiver_id', $userId);
        })->where('status', 'accepted')->first();

        if ($friendship) {
            $friendship->delete();
            return back()->with('success', 'Friend removed.');
        }

        return back()->withErrors('Friendship not found.');
    }



    //show friend request
public function incomingRequests(Request $request)
{
    $userId = auth()->id();
    $search = $request->query('q');

    // Pending requests where current user is receiver
    $requests = Friendship::with('sender')
        ->where('receiver_id', $userId)
        ->where('status', 'pending')
        ->when($search, function ($query, $search) {
            $query->whereHas('sender', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        })
        ->get();

    // --- Get friends list (for right sidebar contacts) ---
    $friends = User::where('id', '!=', $userId)
        ->whereIn('id', function ($query) use ($userId) {
            $query->selectRaw('CASE 
                    WHEN sender_id = ? THEN receiver_id
                    ELSE sender_id END', [$userId])
                ->from('friendships')
                ->where('status', 'accepted')
                ->where(function ($q) use ($userId) {
                    $q->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
                });
        })
        ->get(['id', 'name', 'image']);

        

    $searchResults = collect();
    
    if ($search) {
        // Get users matching search, exclude current user
        $users = User::where('id', '!=', $userId)
            ->where('name', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'image']);

        // For each user, find friendship status
        $searchResults = $users->map(function ($user) use ($userId) {
            $friendship = Friendship::where(function ($q) use ($userId, $user) {
                $q->where('sender_id', $userId)->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($userId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $userId);
            })->first();


            $friendshipStatus = 'not_friends';
            if ($friendship) {
                if ($friendship->status === 'pending') {
                    $friendshipStatus = $friendship->sender_id === $userId ? 'pending_sent' : 'pending_received';
                } elseif ($friendship->status === 'accepted') {
                    $friendshipStatus = 'friends';
                }
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'image' => $user->image,
                'friendshipStatus' => $friendshipStatus,
            ];
        });
    }

    return Inertia::render('User/FriendRequests', [
        'requests' => $requests,
        'searchResults' => $searchResults,
        'searchTerm' => $search,
        'friends' => $friends->map(fn($friend) => [
                'id' => $friend->id,
                'name' => $friend->name,
                'image' => $friend->image
                    ? '/storage/' . $friend->image
                    : 'https://i.pinimg.com/736x/15/0f/a8/150fa8800b0a0d5633abc1d1c4db3d87.jpg',
            ]),
    ]);
}





    public function search(Request $request)
    {
        $keyword = $request->get('q', '');

        // Do not show current user in search results
        $query = User::query()
            ->where('id', '!=', auth()->id())
            ->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%");
            })
            ->limit(10)
            ->get();

        return response()->json($query);
    }



}