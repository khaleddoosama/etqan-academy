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

    public function getTotal(array $data)
    {
        $totalCart =  $this->cartService->getTotalPriceForUser();

        return $this->apply($data['code'], $totalCart);
    }

    public function apply(string $code, float $total): float
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->isValid()) {
            throw new \Exception("Invalid or expired coupon.");
        }

        if ($coupon->type === 'fixed') {
            return max($total - $coupon->discount, 0);
        }

        if ($coupon->type === 'percentage') {
            return $total - ($total * ($coupon->discount / 100));
        }

        return $total;
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
