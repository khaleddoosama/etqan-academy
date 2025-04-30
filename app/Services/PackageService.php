<?php

namespace App\Services;

use App\Repositories\Contracts\PackageRepositoryInterface;

class PackageService
{
    public function __construct(protected PackageRepositoryInterface $repository) {}

    public function getAll()
    {
        return $this->repository->all();
    }

    public function find($id)
    {
        return $this->repository->find($id, ['*'], ['packagePlans']);
    }

    public function findBySlug($slug)
    {
        return $this->repository->findBy('slug', $slug, ['*'], ['packagePlans']);
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
        $package = $this->find($id);
        return $this->repository->updateWithItems($package, $packageData, 'packagePlans', $plansData);
    }


    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
