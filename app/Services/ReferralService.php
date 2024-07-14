<?php
// app/Services/SectionService.php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class ReferralService
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // add
    public function add(User $newUser, $parent_code, $points)
    {
        $parent_user = $this->userService->getUserByCode($parent_code);

        $referral = $parent_user->referralsParent()->create([
            'new_user' => $newUser->id,
            'points' => $points,
        ]);

        $parent_user->increment('points', $points);


        return $referral;
    }
}
