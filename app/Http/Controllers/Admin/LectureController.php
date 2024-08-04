<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LectureRequest;
use App\Jobs\ConvertVideoForStreaming;
use App\Jobs\DuplicateLecture;
use App\Jobs\ProcessVideo;
use App\Models\Lecture;
use App\Services\AwsS3Service;
use App\Services\LectureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class LectureController extends Controller
{
    use ApiResponseTrait;
    protected $lectureService;
    protected $awsS3Service;
    public function __construct(LectureService $lectureService, AwsS3Service $awsS3Service)
    {
        $this->lectureService = $lectureService;
        $this->awsS3Service = $awsS3Service;
    }

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }

    //generatePresignedUrl
    public function generatePresignedUrl(LectureRequest $request)
    {
        try {
            $data = $request->validated();

            $section = $this->lectureService->getSection($data['section_id']);
            $slug = SlugService::createSlug(Lecture::class, 'slug', $data['title']);

            $fileName = 'uploads/' . str_replace(' ', '-', strtolower($section->course->slug)) . '/' . str_replace(' ', '-', strtolower($section->slug)) . '/' . str_replace(' ', '-', strtolower($slug)) . '/videos' . '/' . hexdec(uniqid()) . '.mp4';

            // $fileName = $data['video']->getClientOriginalName();

            $url = $this->awsS3Service->getPreSignedUrl($fileName, 'video/mp4');

            return $this->apiResponse(['url' => $url, 'filename' => $fileName], __('messages.presigned_url_generated'), 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }


    public function store(LectureRequest $request)
    {
        $data = $request->validated();

        $lecture = $this->lectureService->createLecture($data);
        $lecture->video = $request['video_path'];
        $lecture->save();

        // ConvertVideoForStreaming::dispatch($lecture);
        ProcessVideo::dispatch($lecture);

        Toastr::success(__('messages.lecture_created'), __('status.success'));
        return response()->json(['message' => __('messages.lecture_created')]);
    }


    public function show(string $id)
    {
        //
    }


    public function edit(string $id)
    {
        //
    }


    public function update(LectureRequest $request, Lecture $lecture)
    {
        $data = $request->validated();

        // $this->lectureService->updateLecture($lecture, $data) ? Toastr::success(__('messages.lecture_updated'), __('status.success')) : '';
        $lecture = $this->lectureService->updateLecture($lecture, $data);

        // check if data has video or thumbnail
        if ($request->hasFile('video')) {
            ProcessVideo::dispatch($lecture);
        }

        Toastr::success(__('messages.lecture_updated'), __('status.success'));

        return response()->json(['message' => __('messages.lecture_updated')]);
    }
    public function destroy(string $id)
    {
        $lecture = $this->lectureService->getLecture($id);
        $this->lectureService->deleteLecture($lecture);
        Toastr::success(__('messages.lecture_deleted'), __('status.success'));
        return redirect()->back();
    }

    public function duplicate(Request $request)
    {
        [$lecture, $newLecture] = $this->lectureService->duplicateLecture($request->lecture_id, $request->section_id);


        DuplicateLecture::dispatch($lecture,$newLecture);

        // convert video
        // ConvertVideoForStreaming::dispatch($lectures[1]);

        Toastr::success(__('messages.lecture_duplicated'), __('status.success'));
        return redirect()->back();
    }

    public function updateOrder(Request $request)
    {

        $lectures = $request->get('lectures');

        foreach ($lectures as $order => $id) {
            $lecture = Lecture::find($id);
            $lecture->position = $order;
            $lecture->save();
        }

        return response()->json([
            'message' => __('messages.lectures_order_updated'),
            'status' => 200
        ]);
    }
}
