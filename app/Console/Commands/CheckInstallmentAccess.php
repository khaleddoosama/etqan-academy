<?php

namespace App\Console\Commands;

use App\Events\CourseRevokeSoonEvent;
use App\Models\StudentInstallment;
use App\Services\UserCoursesService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckInstallmentAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-installment-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke course access for overdue installments';

    protected $userCoursesService;

    public function __construct(UserCoursesService $userCoursesService)
    {
        parent::__construct();
        $this->userCoursesService = $userCoursesService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get last record of each installment group based on student_id and course_installment_id
        $lastInstallments = StudentInstallment::select('id', 'student_id', 'course_installment_id', 'amount', 'remaining_amount', 'due_date')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('student_installments')
                    ->groupBy('student_id', 'course_installment_id');
            })
            ->get();


        // check if installment due date is past
        foreach ($lastInstallments as $installment) {
            if ($installment->due_date && Carbon::parse($installment->due_date)->isPast()) {
                $this->info('Revoking access for ' . $installment->student->name . ' for ' . $installment->courseInstallment->course->title);

                $this->userCoursesService->changeUserCourseStatus(['status' => 0], $installment->student, $installment->courseInstallment->course);
            }
            // check if is there only one day and it is past
            elseif ($installment->due_date && Carbon::parse($installment->due_date)->isTomorrow()) {
                $this->info('Warning for ' . $installment->student->name . ' for ' . $installment->courseInstallment->course->title);

                event(new CourseRevokeSoonEvent(
                    [$installment->student->id],
                    [
                        "course_title" => $installment->courseInstallment->course->title,
                        "courseSlug" => $installment->courseInstallment->course->slug
                    ]
                ));
            } else {
                $this->info('Access not revoked for ' . $installment->student->name . ' for ' . $installment->courseInstallment->course->title);
            }
        }


        $this->info('Overdue installment access revoked.');
    }
}
