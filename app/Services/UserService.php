<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserService
{
    // get pending users
    public function getPendingUsers()
    {
        return User::studentPending()->get();
    }

    // get active users
    public function getActiveUsers()
    {
        return User::studentActive()->get();
    }

    // get inactive users
    public function getInactiveUsers()
    {
        return User::studentInactive()->get();
    }


    // update user
    public function updateUser(array $data, User $user)
    {
        $user->update($data);
        return $user->wasChanged();
    }



}
