<?php

namespace App\Console\Commands;

use App\Models\UserCourse;
use App\Services\UserCoursesService;
use Illuminate\Console\Command;

class RevokeExpiredAccess extends Command
{
    protected $signature = 'app:revoke-expired-access';

    protected $description = 'Deactivate courses for users whose coupon-based access expired';

    public function handle(UserCoursesService $userCoursesService): int
    {
        $now = now();
        $expired = UserCourse::whereNotNull('expires_at')
            ->where('status', 1)
            ->where('expires_at', '<=', $now)
            ->get();

        $count = 0;
        foreach ($expired as $uc) {
            $userCoursesService->changeUserCourseStatus(['status' => 0], $uc->student, $uc->course);
            $count++;
        }

        $this->info("Revoked access for {$count} user course(s).");
        return Command::SUCCESS;
    }
}
