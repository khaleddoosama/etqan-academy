<?php
// app/Services/InquiryService.php

namespace App\Services;

use App\Models\Gallery;
use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class GalleryService
{
    public function getGalleries(): Collection
    {
        return auth()->user()->galleries;
    }

    public function getGallery($id): Gallery
    {
        return Gallery::findOrfail($id);
    }

    public function createGallery(array $data)
    {
        $data['disk'] = 's3';
        Log::info('from GalleryService data: ' . json_encode($data));
        $gallery = Gallery::create($data);
        return $gallery;
    }

    public function deleteGallery($id)
    {
        $gallery = Gallery::where('id', $id)->where('user_id', auth()->id())->first();
        if ($gallery) {
            $gallery->delete();
            return true;
        } else {
            return false;
        }
    }
}
