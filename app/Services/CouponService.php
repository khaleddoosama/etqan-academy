<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function find($id)
    {
        return Coupon::find($id);
    }

    public function findByCode($code)
    {
        return Coupon::where('code', $code)->first();
    }

    public function getAll()
    {
        return Coupon::latest()->get();
    }

    public function store(array $data)
    {
        return Coupon::create($data);
    }

    public function update($id, array $data)
    {
        $coupon = $this->find($id);

        $coupon->update($data);

        return $coupon->wasChanged();
    }

    public function checkCoupon($code)
    {
        $totalCart =  $this->cartService->getTotalPriceForUser(auth('api')->id());

        $data= $this->apply($code, $totalCart);
        $data['total_before_coupon'] = $totalCart;

        return $data;
    }

    public function apply(string $code, float $total): array
    {
        $coupon = $this->getValidCoupon($code);

        if ($coupon->type === 'fixed') {
            $discount = $coupon->discount;
            $finalTotal = $this->applyFixedDiscount($total, $discount);
        } elseif ($coupon->type === 'percentage') {
            $discount = $total * ($coupon->discount / 100);
            $finalTotal = $this->applyPercentageDiscount($total, $coupon->discount);
        } else {
            return [
                'total_after_coupon' => $total,
                'discount_amount' => 0,
                'discount' => 0,
                'type' => null
            ];
        }

        return [
            'total' => $finalTotal,
            'discount_amount' => $discount,
            'discount' => $coupon->discount,
            'type' => $coupon->type
        ];
    }


    protected function getValidCoupon(string $code)
    {
        $coupon = $this->findByCode($code);

        if (!$coupon || !$coupon->isValid()) {
            throw new \Exception("Invalid or expired coupon.");
        }

        return $coupon;
    }

    protected function applyFixedDiscount(float $total, float $discount): float
    {
        return max($total - $discount, 0);
    }

    protected function applyPercentageDiscount(float $total, float $percentage): float
    {
        return $total - ($total * ($percentage / 100));
    }


    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    public function changeStatus($id, $status): bool
    {
        $coupon = $this->find($id);

        $coupon->update(['status' => $status]);

        return $coupon->wasChanged();
    }
}
