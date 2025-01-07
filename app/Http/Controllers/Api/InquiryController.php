<?php

namespace App\Http\Controllers\Api;

use App\Events\SentInquiryEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InquiryRequest;
use App\Notifications\InquirySentNotification;
use App\Services\AdminNotificationService;
use App\Services\InquiryService;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    use ApiResponseTrait;
    protected InquiryService $inquiryService;
    private AdminNotificationService $adminNotificationService;

    public function __construct(InquiryService $inquiryService, AdminNotificationService $adminNotificationService)
    {
        $this->inquiryService = $inquiryService;
        $this->adminNotificationService = $adminNotificationService;
    }

    public function sendInquiry(InquiryRequest $request)
    {
        $data = $request->validated();
        $inquiry = $this->inquiryService->sendInquiry($data);

        if ($inquiry) {
            event(new SentInquiryEvent([], ['inquiry_id' => $inquiry->id]));

            return $this->apiResponse($inquiry, __('messages.inquiry_sent'), 201);
        } else {
            return $this->apiResponse(null, __('messages.inquiry_not_sent'), 400);
        }
    }
}
