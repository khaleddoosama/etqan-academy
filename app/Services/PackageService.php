<?php

namespace App\Services;

use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;

class PackageService
{
    public function __construct(protected PackageRepositoryInterface $repository) {}

    public function getAll()
    {
        return $this->repository->all();
    }

    public function get($id)
    {
        return $this->repository->find($id, ['*'], ['packagePlans']);
    }

    public function store(array $data)
    {
        $plansData = $data['plans'];
        unset($data['plans']);
        $packageData =  $data;
        return $this->repository->createWithItems($packageData, 'packagePlans', $plansData);
    }

    public function update(array $data, $id)
    {
        $plansData = $data['plans'];
        unset($data['plans']);
        $packageData =  $data;
        $package = $this->get($id);
        return $this->repository->updateWithItems($package, $packageData, 'packagePlans', $plansData);
    }


    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
