<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AttributesTrait;
use Illuminate\Foundation\Http\FormRequest;

class ProgramRequest extends FormRequest
{
    use AttributesTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:programs,name,' . optional($this->program)->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|image|max:2048',
        ];
    }
}
