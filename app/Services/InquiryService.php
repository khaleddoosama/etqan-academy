<?php
// app/Services/InquiryService.php

namespace App\Services;


use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Collection;

class InquiryService
{
    public function getInquiries(): Collection
    {
        return Inquiry::all();
    }

    public function getInquiry($id): Inquiry
    {
        return Inquiry::findOrfail($id);
    }

    public function reply($id)
    {
        $inquiry = Inquiry::findOrfail($id);
        $inquiry->status = "replied";
        $inquiry->save();
        return true;
    }

    public function sendInquiry(array $data)
    {
        $inquiry = Inquiry::create($data);
        return $inquiry;
    }
}
