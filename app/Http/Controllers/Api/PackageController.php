<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PackageRequest;
use App\Http\Resources\PackageResource;
use App\Services\PackageService;

class PackageController extends Controller
{

    use ApiResponseTrait;

    protected $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    public function index()
    {
        $packages = $this->packageService->getAll();
        return $this->apiResponse(PackageResource::collection($packages), 'ok', 200);
    }

    public function show($id)
    {
        $package = $this->packageService->get($id);
        return $this->apiResponse(new PackageResource($package), 'ok', 200);
    }
}
