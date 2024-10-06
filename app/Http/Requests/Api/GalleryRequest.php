<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\AttributesTrait;
use Illuminate\Foundation\Http\FormRequest;

class GalleryRequest extends FormRequest
{
    use AttributesTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required',
            'path' => 'required|file'
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['user_id'] = auth()->user()->id;
        return $data;
    }
}
