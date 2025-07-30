<?php

namespace App\Http\Resources;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class AllPostsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
   
    public function toArray($request)
    {
        
        return $this->collection->map(function($item) {

            return [
                'id' => $item->id,
                'text' => $item->text,
                'image' => $item->image,
                'visibility' => $item->visibility,
                'created_at' => $item->created_at->format(' M D Y'),
                'likes_count' => $item->likes->count(),
                'user_liked' => $item->likes->contains('user_id', Auth::id()), // true/false
                'comments' => $item->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'text' => $comment->text,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                            'image' => $comment->user->image
                        ],
                    ];
                }),
                'user' => [
                    'id' => $item->user->id,
                    'name' => $item->user->name,
                    'image' => $item->user->image
                ],

                
            ];
        });
    }
}
