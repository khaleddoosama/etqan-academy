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
            'password_confirmation' => __('attributes.password_confirmation'),
            'new_password' => __('attributes.new_password'),
            'new_password_confirmation' => __('attributes.new_password_confirmation'),
            'old_password' => __('attributes.old_password'),
            'remember' => __('attributes.remember'),
            'code' => __('attributes.code'),
            'title' => __('attributes.title'),
            'description' => __('attributes.description'),
            'content' => __('attributes.content'),
            'type' => __('attributes.type'),
            'price' => __('attributes.price'),
            'discount' => __('attributes.discount'),
            'start_date' => __('attributes.start_date'),
            'end_date' => __('attributes.end_date'),
            'attributes' => __('attributes.attributes'),
            'path' => __('attributes.file'),
            'course_slug' => __('attributes.course'),
            'points' => __('attributes.points'),
            'wallet_phone' => __('attributes.wallet_phone'),
            
        ];
    }
}
