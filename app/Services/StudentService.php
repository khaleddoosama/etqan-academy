<?php

namespace App\Services;

use App\Models\User;

class StudentService
{
    // search students
    public function searchStudents($query)
    {
        return User::student()
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                    ->orWhere('last_name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->get(['slug', 'first_name', 'last_name', 'email', 'job_title'])
            ->map(function ($user) {
                $user->image = $user->picture_url; // Access the picture_url attribute
                return $user;
            });
    }

    public function getStudentProfile($slug)
    {
        return User::student()->where('slug', $slug)->firstOrFail();
    }
}
