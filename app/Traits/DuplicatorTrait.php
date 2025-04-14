<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Services\SlugService;

trait DuplicatorTrait
{
    public function duplicateModelWithSlug(Model $model, string $slugField, array $overrides = []): Model
    {
        $newModel = $model->replicate(array_keys($overrides));

        foreach ($overrides as $key => $value) {
            $newModel->{$key} = $value;
        }

        $newModel->{$slugField} = SlugService::createSlug(get_class($model), $slugField, $model->title);
        $newModel->save();

        return $newModel;
    }
}
