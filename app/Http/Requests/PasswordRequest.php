<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    use AttributesTrait;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'old_password' => 'sometimes|current_password',
            'new_password' => 'required|confirmed|min:8',
        ];
    }

    // return new_password as password
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['password'] = $data['new_password'];
        unset($data['new_password']);
        return $data;
    }

}
