<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{

    public function show(Section $section)
    {
        $section->load('lectures');
        $sections = Section::where('course_id', $section->course_id)->get();
        $lectures = Lecture::get();
        return view('admin.section.show', compact('section', 'sections', 'lectures'));
    }
}
