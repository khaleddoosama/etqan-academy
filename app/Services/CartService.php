<?php

namespace App\Services;

use App\Models\Cart;

class CartService
{
    protected StudentInstallmentService $studentInstallmentService;

    public function __construct(StudentInstallmentService $studentInstallmentService)
    {
        $this->studentInstallmentService = $studentInstallmentService;
    }

    public function getForUser()
    {
        return Cart::forUser(auth()->user()->id)->latest()->get();
    }

    public function store(array $data)
    {

        $userId = auth('api')->id();
        $courseInstallmentId = $data['course_installment_id'];

        if ($this->isAlreadyInCart($userId, $courseInstallmentId)) {
            throw new \Exception('You have already added this course to your cart');
        }

        if ($this->isAlreadyPurchased($userId, $courseInstallmentId)) {
            throw new \Exception('You have already purchased this course');
        }

        return Cart::create($data);
    }

    protected function isAlreadyInCart(int $userId, int $courseInstallmentId): bool
    {
        return Cart::unique($userId, $courseInstallmentId)->exists();
    }

    protected function isAlreadyPurchased(int $userId, int $courseInstallmentId): bool
    {
        return $this->studentInstallmentService->checkUserAndCourse($userId, $courseInstallmentId);
    }

    public function getTotalPriceForUser()
    {
        // return Cart::getTotalPriceForUser(auth('api')->id());
        $carts = Cart::forUser(auth('api')->id())->get();
        return $carts->sum(function ($cart) {
            return $this->getTotalPriceForOneCart($cart);
        });
    }

    public function getTotalPriceForOneCart($cart)
    {
        return $this->studentInstallmentService->getNextInstallmentPrice($cart->user_id, $cart->course_installment_id);
    }

    public function delete($cartId): bool
    {
        $cart = Cart::where('id', $cartId)->where('user_id', auth('api')->id())->first();
        $cart->delete();
        return true;
    }
}
