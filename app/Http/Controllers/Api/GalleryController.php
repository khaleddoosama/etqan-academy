<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GalleryRequest;
use App\Services\GalleryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GalleryController extends Controller
{
    use ApiResponseTrait;

    protected GalleryService $galleryService;

    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }

    public function store(GalleryRequest $request)
    {
        $data = $request->validated();

        $gallery = $this->galleryService->createGallery($data);
        return $this->apiResponse($gallery, __('messages.gallery_created'), 201);
    }


    public function destroy($id)
    {
        $gallery = $this->galleryService->deleteGallery($id);
        return $this->apiResponse($gallery, __('messages.gallery_deleted'), 200);
    }
}
