<?php

namespace App\Http\Controllers\Api;

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
            $notification = new InquirySentNotification($inquiry->id);
            $this->adminNotificationService->notifyAdmins($notification,['inquiry.list', 'inquiry.show']);

            return $this->apiResponse($inquiry, 'Inquiry sent successfully', 201);
        } else {
            return $this->apiResponse(null, 'Inquiry not sent', 400);
        }
    }
}
