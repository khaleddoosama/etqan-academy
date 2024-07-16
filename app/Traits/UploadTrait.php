<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

trait UploadTrait
{
    // upload Image
    public function uploadImage(UploadedFile $picture, $folderName, $width = 640, $height = 480, $disk = 'public')
    {

        $name_gen = hexdec(uniqid()) . '.' . $picture->getClientOriginalExtension();
        $path = "{$folderName}/{$name_gen}";

        // Ensure the directory exists or create it
        // $this->ensureDirectoryExists($folderName);

        // Image::make($picture)->resize($width, $height)->save(public_path("{$path}"));
        // Image::read($picture)->resize($width, $height)->save(public_path("{$path}"));

        $image = Image::read($picture)->resize($width, $height);
        Storage::disk($disk)->put($path, (string) $image->encode());

        return $path;
    }


    // upload file (pdf)
    public function uploadFile(UploadedFile $file, $folderName, $disk = 'public')
    {

        $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
        $path = "{$folderName}/{$name_gen}";
        // $file->move(public_path("{$folderName}/"), $name_gen);

        // $this->ensureDirectoryExists($folderName);

        // $file->storeAs("{$folderName}/", $name_gen, 'public');
        Storage::disk($disk)->put($path, file_get_contents($file));
        // log absolute path
        Log::info("File uploaded successfully:" . public_path("storage/{$folderName}/{$name_gen}"));
        Log::info("File uploaded successfully:" . public_path("{$folderName}/{$name_gen}"));
        Log::info("File uploaded successfully:" . storage_path('app/public/' . $this->lecture->video));
        Log::info("File uploaded successfully: {$path}");
        return $path;
    }

    // delete Image
    public function deleteIfExists($path)
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        if ($path && Storage::disk('s3')->exists($path)) {
            Storage::disk('s3')->delete($path);
        }

        return true;
    }

    // upload attachments (audios, videos) must return json
    public function uploadAttachments(array $attachments, $folderName): array
    {
        $attachmentData = [];
        foreach ($attachments as $attachment) {
            $name_gen = hexdec(uniqid()) . '.' . $attachment->getClientOriginalExtension();
            $path = "{$folderName}/{$name_gen}";
            $attachment->move(public_path("{$folderName}/"), $name_gen);
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
    // public function ensureDirectoryExists($folderName)
    // {
    //     if (!is_dir(public_path("uploads/{$folderName}/"))) {
    //         mkdir(public_path("uploads/{$folderName}/"), 0755, true);
    //     }
    // }
}
