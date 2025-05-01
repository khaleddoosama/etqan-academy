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
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'meaning_description' => 'nullable|string',
            'logo' => 'nullable|image|max:4096',
            'features' => 'nullable',
            'programs' => 'nullable|array',
            'programs.*' => 'nullable|exists:programs,id',

            'plans' => 'nullable|array',
            'plans.*.id' => 'nullable|exists:package_plans,id',
            'plans.*.title' => 'nullable|string',
            'plans.*.from' => 'nullable|string',
            'plans.*.price' => 'nullable|numeric',
            'plans.*.duration' => 'nullable|numeric',
            'plans.*.device_limit' => 'nullable|numeric',
            'plans.*.number_of_downloads' => 'nullable|numeric',
            'plans.*.description' => 'nullable|string',
            'plans.*.has_ai_access' => 'nullable',
            'plans.*.has_flaticon_access' => 'nullable',
            'plans.*.programs' => 'nullable|array',
            'plans.*.programs.*' => 'nullable|exists:programs,id',
            'plans.*.logo' => 'nullable|image|max:4096',
        ];
    }

    // after validation
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['features'] = explode(',', $data['features']);

        foreach ($data['plans'] as $key => $plan) {
            // check is has ai access or not
            if (isset($plan['has_ai_access']) && $plan['has_ai_access'] == 'on') {
                $data['plans'][$key]['has_ai_access'] = true;
            } else {
                $data['plans'][$key]['has_ai_access'] = false;
            }
            // check is has flaticon access or not
            if (isset($plan['has_flaticon_access']) && $plan['has_flaticon_access'] == 'on') {
                $data['plans'][$key]['has_flaticon_access'] = true;
            } else {
                $data['plans'][$key]['has_flaticon_access'] = false;
            }

            if (empty($plan['id']) && empty($plan['title']) && empty($plan['from']) && empty($plan['price']) && empty($plan['duration'])) {
                unset($data['plans'][$key]);
            }
        }



        return $data;
    }
}
