<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    //index
    public function index()
    {
        $contacts = ContactUs::all();
        return view('admin.support.index', compact('contacts'));
    }
}
