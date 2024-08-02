<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\AttributesTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class LectureRequest extends FormRequest
{
    use AttributesTrait;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $lecture = $this->lecture; // This assumes you're passing the lecture ID in the request somehow.

        Log::info($this->lecture);
        $rules = [
            //unique for title and section
            'title' => [
                'required',
                // Use Rule::unique() to define a more complex uniqueness rule.
                Rule::unique('lectures')->where(function ($query) {
                    $query->where('section_id', $this->section_id);
                    if ($this->lecture) {
                        $query->where('id', '!=', $this->lecture->id);
                    }
                }),
            ],
            'section_id' => 'required|exists:sections,id',
            'description' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240', // max 10mb
            'thumbnail' => 'nullable|image|max:10240', // max 10mb
        ];

        // Check if video is being updated
        // if ($this->isMethod('put')) {
        //     $rules['video'] = 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime,video/3gpp,video/x-msvideo,video/x-flv,video/x-ms-wmv';
        // } else {
        //     $rules['video'] = 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime,video/3gpp,video/x-msvideo,video/x-flv,video/x-ms-wmv';
        // }

        return $rules;
    }
}
