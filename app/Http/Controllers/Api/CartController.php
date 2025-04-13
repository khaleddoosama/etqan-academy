<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CartRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;

class CartController extends Controller
{

    use ApiResponseTrait;

    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function getForUser()
    {
        $items = $this->cartService->getForUser();
        return $this->apiResponse(CartResource::collection($items), 'ok', 200);
    }

    public function store(CartRequest $request)
    {
        $item = $this->cartService->store($request->validated());
        return $this->apiResponse(new CartResource($item), 'ok', 201);
    }

    public function destroy($cartId)
    {
        $item = $this->cartService->delete($cartId);
        return $this->apiResponse(new CartResource($item), 'ok', 200);
    }
}
