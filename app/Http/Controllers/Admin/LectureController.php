<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LectureRequest;
use App\Jobs\ConvertVideoForStreaming;
use App\Models\Lecture;
use App\Services\LectureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Http\UploadedFile;

class LectureController extends Controller
{

    protected $lectureService;
    public function __construct(LectureService $lectureService)
    {
        $this->lectureService = $lectureService;
    }

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(LectureRequest $request)
    {
        $data = $request->validated();
        $lecture = $this->lectureService->createLecture($data);


        ConvertVideoForStreaming::dispatch($lecture);

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
        if ($request->hasFile('video') || $request->hasFile('thumbnail')) {
            ConvertVideoForStreaming::dispatch($lecture);
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
        $lectures = $this->lectureService->duplicateLecture($request->lecture_id, $request->section_id);

        // get video from converted video table
        $quality = $lectures[0]->quality;
        $video = $lectures[0]->convertedVideo->{'mp4_Format_' . $quality};

        // get video from storage
        $videoUrl = Storage::url($video);
        $thumbnailUrl = Storage::url($lectures[0]->thumbnail);
        $videoContent = file_get_contents($videoUrl);
        $thumbnailContent = file_get_contents($thumbnailUrl);

        // Save the video content to a temporary file
        $tempFilePath = tempnam(sys_get_temp_dir(), 'video');
        file_put_contents($tempFilePath, $videoContent);
        // حفظ محتوى الصورة مؤقتًا
        $tempThumbnailPath = tempnam(sys_get_temp_dir(), 'thumbnail');
        file_put_contents($tempThumbnailPath, $thumbnailContent);

        // Create an UploadedFile instance from the temporary file
        $videoFile = new UploadedFile($tempFilePath, basename($videoUrl), 'video/mp4', null, true);
        $thumbnailFile = new UploadedFile($tempThumbnailPath, basename($thumbnailUrl), null, null, true); // يمكنك تعديل نوع الملف حسب الحاجة


        $lectures[1]->video = $videoFile;
        $lectures[1]->thumbnail = $thumbnailFile;
        $lectures[1]->save();

        // convert video
        ConvertVideoForStreaming::dispatch($lectures[1]);

        Toastr::success(__('messages.lecture_duplicated'), __('status.success'));
        return redirect()->back();
    }

    public function updateOrder(Request $request)
    {

        $lectures = $request->get('lectures');

        foreach ($lectures as $order => $id) {
            $lecture = Lecture::find($id);
            $lecture->index = $order;
            $lecture->save();
        }

        return response()->json([
            'message' => __('messages.lectures_order_updated'),
            'status' => 200
        ]);
    }
}
