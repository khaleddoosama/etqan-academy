<?php

namespace App\Services;

use App\Models\Package;
use Illuminate\Database\Eloquent\Collection;

class PackageService
{
    public function getAllPackages(): Collection
    {
        return Package::get();
    }

    // create package
    public function createPackage(array $data): Package
    {
        return Package::create($data);
    }

    // update package
    public function updatePackage(array $data, Package $package): bool
    {
        $package->update($data);

        return $package->wasChanged();
    }

    // delete package
    public function deletePackage(Package $package): bool
    {
        return $package->delete();
    }
}
