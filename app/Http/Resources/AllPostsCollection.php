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
                'comments' => $item->comments
                    ->whereNull('parent_id')
                    ->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'text' => $comment->text,
                            'parent_id' => $comment->parent_id,
                            'likes_count_comment' => $comment->likes->count(),
                            'user_comment' => $comment->likes->contains('user_id', Auth::id()), // true/false
                            'user' => [
                                'id' => $comment->user->id,
                                'name' => $comment->user->name,
                                'image' => $comment->user->image ? '/storage/' . $comment->user->image : null,
                            ],
                           'replies' => $comment->replies->map(function ($reply) {
                                return [
                                    'id' => $reply->id,
                                    'text' => $reply->text,
                                    'parent_id' => $reply->parent_id,
                                    'likes_count_comment' => $reply->likes->count(),
                                    'user_comment' => $reply->likes->contains('user_id', Auth::id()), // true/false
                                    'replies' => $reply->replies, // recursion
                                    'user' => [
                                        'id' => $reply->user->id,
                                        'name' => $reply->user->name,
                                        'image' => $reply->user->image ? '/storage/' . $reply->user->image : null,
                                    ],
                                ];
                            }),
                        ];
                    }),
                'user' => [
                    'id' => $item->user->id,
                    'name' => $item->user->name,
                    'image' => $item->user->image
                ],

                
            ];
        });

    //  return $this->collection->map(function($item) {

    //     return [
    //         'id' => $item->id,
    //         'text' => $item->text,
    //         'image' => $item->image,
    //         'visibility' => $item->visibility,
    //         'created_at' => $item->created_at->format('M d Y'),
    //         'likes_count' => $item->likes->count(),
    //         'user_liked' => $item->likes->contains('user_id', Auth::id()),
    //         'comments' => $item->comments->map(function ($comment) {
    //             return [
    //                 'id' => $comment->id,
    //                 'text' => $comment->text,
    //                 'parent_id' => $comment->parent_id,
    //                 'user' => [
    //                     'id' => $comment->user->id,
    //                     'name' => $comment->user->name,
    //                     'image' => $comment->user->image ? '/storage/' . $comment->user->image : null,
    //                 ],
    //                 // include replies here if any (recursive)
    //                 'replies' => $comment->replies->map(function ($reply) {
    //                     return [
    //                         'id' => $reply->id,
    //                         'text' => $reply->text,
    //                         'parent_id' => $reply->parent_id,
    //                         'user' => [
    //                             'id' => $reply->user->id,
    //                             'name' => $reply->user->name,
    //                             'image' => $reply->user->image ? '/storage/' . $reply->user->image : null,
    //                         ],
    //                     ];
    //                 }),
    //             ];
    //         }),
    //         'user' => [
    //             'id' => $item->user->id,
    //             'name' => $item->user->name,
    //             'image' => $item->user->image ? '/storage/' . $item->user->image : null,
    //         ],
    //     ];
    // });



    }
}
