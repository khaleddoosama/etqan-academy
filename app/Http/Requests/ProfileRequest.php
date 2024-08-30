<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . auth()->user()->id . ',id',
            'phone' => ['required', 'string', 'max:255'],
            'picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ];
    }
}
