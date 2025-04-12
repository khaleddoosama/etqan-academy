<?php

namespace App\Services\Support;

use Illuminate\Support\Str;

class SlugResolverService
{
    public function resolveSlugs(array $data, array $slugMap): array
    {
        foreach ($slugMap as $slugKey => $modelClass) {
            if (!isset($data[$slugKey])) {
                continue;
            }

            $idField = Str::replaceLast('_slug', '_id', $slugKey);

            $model = $modelClass::where('slug', $data[$slugKey])->firstOrFail();

            $data[$idField] = $model->id;
            unset($data[$slugKey]);
        }

        return $data;
    }
}
