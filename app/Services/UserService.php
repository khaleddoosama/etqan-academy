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
        return User::studentActive()->orderBy('last_login', 'desc')->get();
    }

    // get inactive users
    public function getInactiveUsers()
    {
        return User::studentInactive()->orderBy('last_login', 'desc')->get();
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

    // get user by id
    public function getUser($id)
    {
        return User::where('id', $id)->where('role', 'student')->first();
    }

    // create user
    public function createUser(array $data)
    {
        return User::create($data);
    }

    // update user
    public function updateUser(array $data, User $user)
    {
        $user->update($data);
        return $user->wasChanged();
    }

    //getStudentByPhone
    public function getStudentByPhone($phone)
    {
        // إزالة أي مسافات أو شرطات
        $phone = str_replace([' ', '-'], '', $phone);

        // التأكد من أن الرقم يطابق صيغة بدون رمز الدولة أو مع رمز الدولة
        return User::where(function ($query) use ($phone) {
            $query->where('phone', $phone)
                ->orWhere('phone', '0' . substr($phone, -10))
                ->orWhere('phone', '+2' . substr($phone, -10));
        })->where('role', 'student')->first();
    }
}
