<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\AttributesTrait;
use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    use AttributesTrait;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'programs' => 'nullable|array',
            'programs.*' => 'nullable|exists:programs,id',
            'plans' => 'required|array',
            'plans.*.id' => 'nullable|exists:package_plans,id',
            'plans.*.from' => 'nullable|string',
            'plans.*.price' => 'nullable|numeric',
            'plans.*.duration' => 'nullable|numeric',
            'plans.*.device_limit' => 'nullable|numeric',
            'plans.*.description' => 'nullable|string',
            'plans.*.programs' => 'nullable|array',
            'plans.*.programs.*' => 'nullable|exists:programs,id',
            'plans.*.logo' => 'nullable|image|max:4096',
        ];
    }
}
