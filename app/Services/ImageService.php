<?php

namespace App\Services;

use Intervention\Image\Facades\Image;

class ImageService
{
    public function updateImage($model, $request)
    {
        $file = $request->file('image');

        // Make image
        $image = Image::make($file);

        // Delete old image if exists and not default placeholder
        if (!empty($model->image)) {
            $currentImage = public_path($model->image);
            if (file_exists($currentImage) && $model->image !== '/images/user-placeholder.png') {
                unlink($currentImage);
            }
        }

        // Crop if width is provided
        if ($request->width) {
            $image->crop(
                intval($request->width),
                intval($request->height),
                intval($request->left),
                intval($request->top)
            );
        }

        // Generate filename
        $extension = $file->getClientOriginalExtension();
        $name = time() . '.' . $extension;

        // Save new image
        $image->save(public_path('/images/' . $name));

        // Update model image path
        $model->image = '/images/' . $name;

        return $model;
    }
}
