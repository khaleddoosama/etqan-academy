<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $category = Category::factory()->create();

        return [
            'title' => $this->faker->unique()->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            'category_id' => $category->id,
            'price' => 1000,
            'discount_price' => null,
            'status' => 1,
        ];
    }
}

