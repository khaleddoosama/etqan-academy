<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CouponRequest;
use App\Services\CouponService;

class CouponController extends Controller
{

    use ApiResponseTrait;

    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    // check coupon
    public function applyCoupon(CouponRequest $request)
    {
        try {
            $data = $request->validated();

            $total = $this->couponService->checkCoupon($data);

            return $this->apiResponse($total, 'Coupon applied successfully', 200);
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 400);
        }
    }
}
