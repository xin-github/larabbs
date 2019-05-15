<?php

namespace App\Handlers;

use Image;

class ImageUploadHandler
{
    protected $allowed_ext = ['jpg', 'png', 'gif', 'jpeg'];

    public function save($file, $folder, $file_prefix, $max_width = false)
    {
        $folder_name = "uploads/images/$folder/" . date("Ym/d", time());

        $upload_path = public_path() . '/' . $folder_name;

        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        $file_name = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;

        if (!in_array($extension, $this->allowed_ext)) {
            return false;
        }

        $file->move($upload_path, $file_name);

        if ($max_width && $extension != 'gif') {

            $this->reduceSize($upload_path . '/' . $file_name, $max_width);
        }

        return [
            'path' => config('app.url') . "/$folder_name/$file_name"
        ];
    }

    public function reduceSize($file_path, $max_width)
    {
        $image = Image::make($file_path);

        $image->resize($max_width, null, function ($constraint) {

            // 设定宽度是 $max_width，高度等比例双方缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        // 对图片修改后进行保存
        $image->save();
    }
}