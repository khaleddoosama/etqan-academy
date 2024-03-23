<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{

    public function show(Section $section)
    {
        $section->load('lectures');
        return view('admin.section.show', compact('section'));
    }
}
