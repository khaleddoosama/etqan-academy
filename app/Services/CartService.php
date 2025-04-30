<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Course;
use App\Models\PackagePlans;
use App\Services\Support\SlugResolverService;

class CartService
{
    public function __construct(
        private SlugResolverService $slugResolver,
        private StudentInstallmentService $studentInstallmentService,
        private UserCoursesService $userCoursesService
    ) {}

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
        $courseId = $data['course_id'] ?? null;
        $packagePlanId = $data['package_plan_id'] ?? null;
        $courseInstallmentId = $data['course_installment_id'] ?? null;

        $this->validateCartAddition($userId, $courseId, $packagePlanId, $courseInstallmentId);

        $data['price'] = $this->calculateCartPrice($userId, $courseId, $packagePlanId, $courseInstallmentId);

        return Cart::create($data);
    }
    protected function validateCartAddition(int $userId, ?int $courseId, ?int $packagePlanId, $courseInstallmentId): void
    {
        if ($courseId && $this->isAlreadyInCartCourse($userId, $courseId)) {
            throw new \Exception('You have already added this course to your cart');
        }

        if ($packagePlanId && $this->isAlreadyInCartPackage($userId, $packagePlanId)) {
            throw new \Exception('You have already added this package to your cart');
        }

        if ($courseId && $this->isAlreadyPurchasedCourse($userId, $courseId, $courseInstallmentId)) {
            throw new \Exception('You have already purchased this course');
        }
    }


    protected function calculateCartPrice(int $userId, ?int $courseId, ?int $packagePlanId, $courseInstallmentId): float
    {
        if ($courseId) {
            $course = Course::find($courseId);

            if ($courseInstallmentId && !in_array($courseInstallmentId, $course->courseInstallments->pluck('id')->toArray())) {
                throw new \Exception('This course installment does not belong to this course');
            }

            if ($courseInstallmentId) {
                return $this->studentInstallmentService->getNextInstallmentPrice($userId, $courseInstallmentId);
            }

            return $course->total_price;
        }

        if ($packagePlanId) {
            $packagePlan = PackagePlans::findOrFail($packagePlanId);
            return $packagePlan->price;
        }

        throw new \Exception('Neither course nor package selected');
    }

    protected function isAlreadyInCartCourse(int $userId, int $courseId): bool
    {
        return Cart::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();
    }

    protected function isAlreadyInCartPackage(int $userId, int $packagePlanId): bool
    {
        return Cart::where('user_id', $userId)
            ->where('package_plan_id', $packagePlanId)
            ->exists();
    }

    protected function isAlreadyPurchasedCourse(int $userId, int $courseId, $courseInstallmentId): bool
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
