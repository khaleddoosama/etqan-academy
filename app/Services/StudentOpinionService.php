<?php

namespace App\Services;

use App\Models\Course;
use App\Models\StudentOpinion;
use App\Services\Support\SlugResolverService;

class StudentOpinionService
{
    protected SlugResolverService $slugResolver;

    public function __construct(SlugResolverService $slugResolver)
    {
        $this->slugResolver = $slugResolver;
    }

    public function all()
    {
        return StudentOpinion::latest()->get();
    }

    public function getForTheWholeSystem()
    {
        return StudentOpinion::theWholeSystem()->approved()->latest()->get();
    }

    public function store(array $data)
    {
        if (isset($data['course_slug'])) {
            $data = $this->slugResolver->resolveSlugs($data, [
                'course_slug' => Course::class,
            ]);
        }
        return StudentOpinion::create($data);
    }

    public function changeStatus($studentOpinionId, $status)
    {
        $studentOpinion = StudentOpinion::findOrFail($studentOpinionId);
        if ($status == 1) {
            $studentOpinion->status = 1;
            $studentOpinion->approved_at = now();
            $studentOpinion->save();
        } elseif ($status == 2) {
            $studentOpinion->status = 2;
            $studentOpinion->rejected_at = now();
            $studentOpinion->save();
        } else {
            $studentOpinion->status = 0;
            $studentOpinion->save();
        }

        return $studentOpinion;
    }
}
