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
        $path = "uploads/{$folderName}/{$name_gen}";

        // Ensure the directory exists or create it
        $this->ensureDirectoryExists($folderName);

        // Image::make($picture)->resize($width, $height)->save(public_path("{$path}"));
        // Image::read($picture)->resize($width, $height)->save(public_path("{$path}"));

        $image = Image::read($picture)->resize($width, $height);
        if ($disk == 's3') {
            Storage::disk($disk)->put($path, (string) $image->encode());
        } elseif ($disk == 'public') {
            // in public path
            $image->save(public_path("{$path}"));
        }

        //reomve // from $path
        $path = str_replace('//', '/', $path);
        return $path;
    }


    // upload file (pdf)
    public function uploadFile(UploadedFile $file, $folderName, $disk = 'public')
    {
        try {
            $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = "uploads/{$folderName}/{$name_gen}";

            if ($disk == 's3') {
                Storage::disk($disk)->put($path, file_get_contents($file));
            } elseif ($disk == 'public') {
                $file->move(public_path("uploads/{$folderName}/"), $name_gen);
            }

            return $path;
        } catch (\Exception $e) {
            Log::error("File upload failed: " . $e->getMessage());
            return false;
        }
    }

    // delete Image
    public function deleteIfExists($path)
    {
        if (!$path) {
            return false;
        }

        $paths = [
            public_path($path),
            public_path('uploads/' . $path),
        ];

        foreach ($paths as $filePath) {
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $disks = ['public', 's3'];
        foreach ($disks as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }

        return true;
    }


    // upload attachments (audios, videos) must return json
    public function uploadAttachments(array $attachments, $folderName): array
    {
        $attachmentData = [];
        foreach ($attachments as $attachment) {
            $name_gen = hexdec(uniqid()) . '.' . $attachment->getClientOriginalExtension();
            $path = "uploads/{$folderName}/{$name_gen}";
            // $attachment->move(public_path("{$folderName}/"), $name_gen);
            Storage::put($path, file_get_contents($attachment));

            $attachmentData[] = [
                'path' => $path,
                'originalName' => $attachment->getClientOriginalName(),
                'type' => $attachment->getMimeType()
            ];
        }
        // Encode the attachment data as JSON

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
