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

        // $this->middleware('permission:course.create')->only('');
        // if have permission course.create or course.edit then allow all methods else only index
        $this->middleware('permission:course.create|course.edit', ['except' => ['index']]);
    }

    // index
    public function index()
    {
        $lectures = $this->lectureService->getAllLectures();
        return view('admin.lecture.index', compact('lectures'));
    }

    //generatePresignedUrl
    public function generatePresignedUrl(LectureRequest $request)
    {
        try {
            $data = $request->validated();

            $section = $this->lectureService->getSection($data['section_id']);
            if (!array_key_exists('id', $data)) {
                $slug = SlugService::createSlug(Lecture::class, 'slug', $data['title']);
            } else {
                $lecture = $this->lectureService->getLecture($data['id']);
                $slug = $lecture->slug;
            }

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
        // $lecture->video = $request['video_path'];
        $lecture->save();

        // ConvertVideoForStreaming::dispatch($lecture);
        // ProcessVideo::dispatch($lecture)->onQueue('low');

        Toastr::success(__('messages.lecture_created'), __('status.success'));
        // return $this->apiResponse($lecture, __('messages.lecture_created'), 201);
        return redirect()->route('admin.lectures.edit', $lecture->id);
    }

    //edit
    public function edit(Lecture $lecture)
    {
        // lecture with course and section
        $lecture->load('section');
        // lecture with course
        $lecture->load('section.course');
        $sections = $this->lectureService->getSections($lecture->section->course->slug);
        return view('admin.lecture.edit', compact('lecture', 'sections'));
    }

    public function update(LectureRequest $request, Lecture $lecture)
    {

        try {
            $data = $request->validated();

            $lecture = $this->lectureService->updateLecture($lecture, $data);

            // check if data has video or thumbnail
            // if ($request->has('video_path')) {
            //     $lecture->video = $request['video_path'];
            //     $lecture->processed = 0;
            //     $lecture->save();
            //     ProcessVideo::dispatch($lecture)->onQueue('low');
            // }

            Toastr::success(__('messages.lecture_updated'), __('status.success'));

            // return $this->apiResponse($lecture, __('messages.lecture_updated'), 200);

            return redirect()->route('admin.lectures.edit', $lecture->id);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Toastr::error($e->getMessage());
            return redirect()->back();
        }
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


        // DuplicateLecture::dispatch($lecture, $newLecture)->onQueue('medium');;

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


    public function updateAttachment(Request $request, Lecture $lecture)
    {
        $lecture = $this->lectureService->updateAttachment($lecture, $request->attachment_path, $request->attachment_name);

        return $this->apiResponse($lecture, __('messages.attachment_updated'), 200);
    }

    public function deleteAttachment(Request $request, Lecture $lecture)
    {

        $this->lectureService->deleteAttachment($lecture, $request->attachment_path);

        Toastr::success(__('messages.attachment_deleted'), __('status.success'));

        return redirect()->back();
    }


    public function changeIsFree(Lecture $lecture)
    {
        $lecture = $this->lectureService->changeIsFree($lecture);

        Toastr::success(__('messages.lecture_updated'), __('status.success'));
        return redirect()->back();
    }

    // get lecture based on section
    public function getLectures($section_id)
    {
        $lectures = $this->lectureService->getLectures($section_id);

        return $this->apiResponse($lectures, 'ok', 200);
    }

    public function updateVideoPath(Request $request, $lectureId)
    {
        $this->validate($request, [
            'video_paths' => 'required|array',
            'video_paths.*' => 'string|nullable',
        ]);

        $result = $this->lectureService->updateVideoPaths($lectureId, $request->input('video_paths'));

        return $this->apiResponse($result, 'Video Pathes Updated Successfully', 200);
    }

    // failedLectures
    // public function failedLectures()
    // {
    //     $lectures = $this->lectureService->getFailedLectures();
    //     return view('admin.lecture.failed', compact('lectures'));
    // }
}
