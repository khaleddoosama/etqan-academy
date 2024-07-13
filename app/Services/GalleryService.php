<?php
// app/Services/InquiryService.php

namespace App\Services;

use App\Models\Gallery;
use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Collection;

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
        $gallery = auth()->user()->galleries()->create($data);
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
