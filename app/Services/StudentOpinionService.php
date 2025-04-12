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
        $studentOpinion->status = $status;
        $studentOpinion->save();
        return $studentOpinion;
    }
}
