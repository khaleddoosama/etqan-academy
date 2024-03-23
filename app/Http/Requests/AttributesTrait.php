<?php

namespace App\Http\Requests;

trait AttributesTrait
{
    public function attributes(): array
    {
        return [
            'name' => __('attributes.name'),
            'email' => __('attributes.email'),
            'password' => __('attributes.password'),
            'image' => __('attributes.image'),
            'role' => __('attributes.role'),
            'phone' => __('attributes.phone'),
            'address' => __('attributes.address'),
            'gender' => __('attributes.gender'),
            'status' => __('attributes.status'),
            'country' => __('attributes.country'),
            'city' => __('attributes.city'),
            'link' => __('attributes.link'),
            'category_id' => __('attributes.category_id'),
            'national_id' => __('attributes.national_id'),
            'national_id_photo_path' => __('attributes.national_id_photo_path'),
            
        ];
    }
}
