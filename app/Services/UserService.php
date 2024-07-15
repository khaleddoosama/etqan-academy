<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserService
{
    // get pending users
    // public function getPendingUsers()
    // {
    //     return User::studentPending()->get();
    // }

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

    // get students
    public function getStudents()
    {
        return User::student()->get();
    }

    // get user by code
    public function getUserByCode($code)
    {
        return User::where('code', $code)->first();
    }

    // update user
    public function updateUser(array $data, User $user)
    {
        $user->update($data);
        return $user->wasChanged();
    }
}
