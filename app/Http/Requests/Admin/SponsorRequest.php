<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AttributesTrait;
use Illuminate\Foundation\Http\FormRequest;

class SponsorRequest extends FormRequest
{
    use AttributesTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'link' => 'required|url',
            'type' => 'required|in:gold,silver,platinum,diamond',

            'image' => 'image|max:2048' .  optional($this->sponsor)->exists ? '' : '|required',
        ];
    }

}
