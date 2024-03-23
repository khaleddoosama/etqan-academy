<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\AttributesTrait;

class LectureRequest extends FormRequest
{
    use AttributesTrait;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $rules = [
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
        ];

        // Check if video is being updated
        if ($this->isMethod('put')) {
            $rules['video'] = 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime,video/3gpp,video/x-msvideo,video/x-flv,video/x-ms-wmv';
        } else {
            $rules['video'] = 'required|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime,video/3gpp,video/x-msvideo,video/x-flv,video/x-ms-wmv';
        }

        return $rules;
    }
}
