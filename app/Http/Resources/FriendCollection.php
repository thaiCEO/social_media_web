<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FriendCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($friend) {
            return [
                'id' => $friend->id,
                'name' => $friend->name,
                'image' => $friend->image
                    ? '/storage/' . $friend->image
                    : 'https://i.pinimg.com/736x/15/0f/a8/150fa8800b0a0d5633abc1d1c4db3d87.jpg',
            ];
        });
    }
}
