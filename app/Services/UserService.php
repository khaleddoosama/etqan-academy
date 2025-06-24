<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Validation\ValidationException;

class UserService
{
    // get pending users
    // public function getPendingUsers()
    // {
    //     return User::studentPending()->get();
    // }

    // get active users
    public function getActiveUsers($perPage = 25, $search = null)
    {
        $query = User::studentActive()->orderBy('last_login', 'desc');

        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    // get inactive users
    public function getInactiveUsers($perPage = 25, $search = null)
    {
        $query = User::studentInactive()->orderBy('last_login', 'desc');

        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
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

    // get user courses
    public function getActiveUserCourses($user_id)
    {
        $userCourses = UserCourse::where('student_id', $user_id)
            ->where('status', UserCourse::STATUS_ACTIVE)
            ->whereHas('course', function ($query) {
                $query->where('status', 1);
            })
            ->get();

        return $userCourses;
    }
}
