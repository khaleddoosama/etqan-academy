<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Course;
use App\Services\Support\SlugResolverService;

class CartService
{
    protected StudentInstallmentService $studentInstallmentService;
    protected SlugResolverService $slugResolver;

    protected UserCoursesService $userCoursesService;
    public function __construct(SlugResolverService $slugResolver, StudentInstallmentService $studentInstallmentService, UserCoursesService $userCoursesService)
    {
        $this->studentInstallmentService = $studentInstallmentService;
        $this->slugResolver = $slugResolver;
        $this->userCoursesService = $userCoursesService;
    }

    public function getForUser($user_id, $columns = ['*'], $with = [])
    {
        return Cart::forUser($user_id)->latest()->with($with)->get($columns);
    }

    public function store(array $data)
    {
        $data = $this->slugResolver->resolveSlugs($data, [
            'course_slug' => Course::class,
        ]);

        $userId = auth('api')->id();
        $courseId = $data['course_id'];
        $courseInstallmentId = $data['course_installment_id'] ?? null;

        $this->validateCartAddition($userId, $courseId, $courseInstallmentId);

        $data['price'] = $this->calculateCartPrice($userId, $courseId, $courseInstallmentId);

        return Cart::create($data);
    }
    protected function validateCartAddition(int $userId, int $courseId, $courseInstallmentId): void
    {
        if ($this->isAlreadyInCart($userId, $courseId)) {
            throw new \Exception('You have already added this course to your cart');
        }

        if ($this->isAlreadyPurchased($userId, $courseId, $courseInstallmentId)) {
            throw new \Exception('You have already purchased this course');
        }
    }

    protected function calculateCartPrice(int $userId, int $courseId, $courseInstallmentId): float
    {
        $course = Course::find($courseId);

        if ($courseInstallmentId && !in_array($courseInstallmentId, $course->courseInstallments->pluck('id')->toArray())) {
            throw new \Exception('This course installment does not belong to this course');
        }

        if ($courseInstallmentId) {
            return $this->studentInstallmentService->getNextInstallmentPrice($userId, $courseInstallmentId);
        }

        return $course->total_price;
    }
    protected function isAlreadyInCart(int $userId, int $courseId): bool
    {
        return Cart::unique($userId, $courseId)->exists();
    }

    protected function isAlreadyPurchased(int $userId, int $courseId, $courseInstallmentId): bool
    {
        if ($courseInstallmentId) {
            return $this->studentInstallmentService->checkUserAndCourse($userId, $courseInstallmentId);
        } else {
            return $this->userCoursesService->checkUserAndCourse($userId, $courseId);
        }
    }

    public function getTotalPriceForUser($userId)
    {
        return Cart::getTotalPriceForUser($userId);
    }

    public function delete($cartId): bool
    {
        $cart = Cart::forUser(auth('api')->id())->find($cartId);
        $cart->delete();
        return true;
    }
}
