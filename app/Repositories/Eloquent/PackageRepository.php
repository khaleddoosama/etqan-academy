<?php

namespace App\Repositories\Eloquent;

use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;

class PackageRepository extends BaseRepository implements PackageRepositoryInterface
{
    protected function model(): Package
    {
        return new Package();
    }
}
