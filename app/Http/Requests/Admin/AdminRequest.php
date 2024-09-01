<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminRequest extends FormRequest
{
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

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->all_admin . ',id'],
            'phone' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(Role::pluck('id')->toArray())],
            'picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ];

        if ($this->method() == 'PUT') {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        } elseif ($this->method() == 'POST') {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }
}
