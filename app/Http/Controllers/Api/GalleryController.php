<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    use ApiResponseTrait;

    protected GalleryService $galleryService;

    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }

    public function store(Request $request)
    {
        $gallery = $this->galleryService->createGallery($request->all());
        return $this->apiSuccessResponse($gallery);
    }
}
