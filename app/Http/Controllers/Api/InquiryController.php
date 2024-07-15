<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InquiryRequest;
use App\Services\InquiryService;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    use ApiResponseTrait;
    protected InquiryService $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;
    }

    public function sendInquiry(InquiryRequest $request)
    {
        $data = $request->validated();
        $inquiry = $this->inquiryService->sendInquiry($data);

        if ($inquiry) {
            return $this->apiResponse($inquiry, 'Inquiry sent successfully', 201);
        } else {
            return $this->apiResponse(null, 'Inquiry not sent', 400);
        }
    }
}
