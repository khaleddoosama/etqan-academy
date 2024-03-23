<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

trait UploadTrait
{
    // upload Image
    public function uploadImage(UploadedFile $picture, $folderName, $width = 640, $height = 480)
    {

        $name_gen = hexdec(uniqid()) . '.' . $picture->getClientOriginalExtension();
        $path = "uploads/{$folderName}/{$name_gen}";

        // Ensure the directory exists or create it
        $this->ensureDirectoryExists($folderName);

        // Image::make($picture)->resize($width, $height)->save(public_path("{$path}"));
        Image::read($picture)->resize($width, $height)->save(public_path("{$path}"));

        return $path;
    }


    // upload file (pdf)
    public function uploadFile(UploadedFile $file, $folderName)
    {
        $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
        $path = "uploads/{$folderName}/{$name_gen}";
        $file->move(public_path("uploads/{$folderName}/"), $name_gen);

        return $path;
    }

    // delete Image
    public function deleteIfExists($path)
    {
        if (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    // upload attachments (audios, videos) must return json
    public function uploadAttachments(array $attachments, $folderName): array
    {
        $attachmentData = [];
        foreach ($attachments as $attachment) {
            $name_gen = hexdec(uniqid()) . '.' . $attachment->getClientOriginalExtension();
            $path = "uploads/{$folderName}/{$name_gen}";
            $attachment->move(public_path("uploads/{$folderName}/"), $name_gen);
            $attachmentData[] = $path;
        }
        // Encode the attachment data as JSON

        // dd($attachmentData);
        return $attachmentData;
    }

    // delete Attachments
    public function deleteAttachments($attachments)
    {
        foreach ($attachments as $attachment) {
            $this->deleteIfExists($attachment['path']);
        }
    }

    // Ensure the directory exists or create it
    public function ensureDirectoryExists($folderName)
    {
        if (!is_dir(public_path("uploads/{$folderName}/"))) {
            mkdir(public_path("uploads/{$folderName}/"), 0755, true);
        }
    }
}
