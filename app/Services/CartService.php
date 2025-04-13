<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Course;
use App\Services\Support\SlugResolverService;

class CartService
{
    protected SlugResolverService $slugResolver;
    protected UserCoursesService $userCoursesService;

    public function __construct(SlugResolverService $slugResolver, UserCoursesService $userCoursesService)
    {
        $this->userCoursesService = $userCoursesService;
        $this->slugResolver = $slugResolver;
    }

    public function getForUser()
    {
        return Cart::forUser(auth()->user()->id)->latest()->get();
    }

    public function store(array $data)
    {
        $data = $this->slugResolver->resolveSlugs($data, [
            'course_slug' => Course::class,
        ]);

        $userId = auth('api')->id();
        $courseId = $data['course_id'];

        if ($this->isAlreadyInCart($userId, $courseId)) {
            throw new \Exception('You have already added this course to your cart');
        }

        if ($this->isAlreadyPurchased($userId, $courseId)) {
            throw new \Exception('You have already purchased this course');
        }

        return Cart::create($data);
    }

    protected function isAlreadyInCart(int $userId, int $courseId): bool
    {
        return Cart::unique($userId, $courseId)->exists();
    }

    protected function isAlreadyPurchased(int $userId, int $courseId): bool
    {
        return $this->userCoursesService->checkUserAndCourse($courseId, $userId);
    }

    public function getTotalPriceForUser()
    {
        return Cart::getTotalPriceForUser(auth('api')->id());
    }

    public function delete($cartId)
    {
        $cart = Cart::where('id', $cartId)->where('user_id', auth('api')->id())->first();
        $cart->delete();
        return $cart;
    }
}
