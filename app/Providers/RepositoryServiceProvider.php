<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->bindRepositories();
    }

    protected function bindRepositories()
    {
        $contractsPath = app_path('Repositories/Contracts');
        $eloquentPath = app_path('Repositories/Eloquent');

        $interfaces = File::allFiles($contractsPath);

        foreach ($interfaces as $interface) {
            $interfaceName = pathinfo($interface->getFilename(), PATHINFO_FILENAME);
            $modelName = str_replace('RepositoryInterface', '', $interfaceName);

            $interfaceClass = "App\\Repositories\\Contracts\\{$interfaceName}";
            $repositoryClass = "App\\Repositories\\Eloquent\\{$modelName}Repository";

            if (class_exists($repositoryClass)) {
                $this->app->bind($interfaceClass, $repositoryClass);
            }
        }
    }

    public function boot()
    {
        //
    }
}
