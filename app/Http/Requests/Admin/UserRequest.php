<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AttributesTrait;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    use AttributesTrait;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $this->user . ',id'],
            'phone' => ['required', 'string', 'max:255'],
            'picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
            'category_id' => ['required', 'exists:categories,id'],
        ];

        if ($this->method() == 'PUT') {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        } elseif ($this->method() == 'POST') {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }
}
