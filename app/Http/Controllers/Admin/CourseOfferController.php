<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseOffer;
use App\Http\Requests\Admin\CourseOfferRequest;
use App\Services\CourseOfferService;
use App\Services\CourseService;
use Yoeunes\Toastr\Facades\Toastr;

class CourseOfferController extends Controller
{
    protected CourseService $courseService;
    protected CourseOfferService $courseOfferService;

    public function __construct(CourseService $courseService, CourseOfferService $courseOfferService)
    {
        $this->courseService = $courseService;
        $this->courseOfferService = $courseOfferService;

        // course_offer.list course_offer.create course_offer.edit course_offer.delete
        $this->middleware('permission:course_offer.list')->only('index');
        $this->middleware('permission:course_offer.create')->only('create', 'store');
        $this->middleware('permission:course_offer.edit')->only('edit', 'update');
        $this->middleware('permission:course_offer.delete')->only('destroy');
    }
    public function index()
    {
        $courseOffers = $this->courseOfferService->getAll();
        return view('admin.course_offer.index', compact('courseOffers'));
    }


    public function create()
    {
        $courses = $this->courseService->getCourses();
        return view('admin.course_offer.create', compact('courses'));
    }

    public function store(CourseOfferRequest $request)
    {
        $data = $request->validated();
        $this->courseOfferService->createCourseOffer($data);
        Toastr::success(__('messages.course_offer_created'), __('status.success'));

        return redirect()->route('admin.course_offers.index');
    }


    public function show(CourseOffer $courseOffer) {}


    public function edit(CourseOffer $courseOffer)
    {
        $courses = $this->courseService->getCourses();
        return view('admin.course_offer.edit', compact('courseOffer', 'courses'));
    }


    public function update(CourseOfferRequest $request, CourseOffer $courseOffer)
    {
        $data = $request->validated();
        $is_changed = $this->courseOfferService->updateCourseOffer($courseOffer, $data);
        $is_changed ? Toastr::success(__('messages.course_offer_updated'), __('status.success')) : '';

        return redirect()->route('admin.course_offers.index');
    }


    public function destroy(CourseOffer $courseOffer)
    {
        $result = $this->courseOfferService->deleteCourseOffer($courseOffer);
        $result ? Toastr::success(__('messages.course_offer_deleted'), __('status.success')) : '';

        return redirect()->route('admin.course_offers.index');
    }
}
