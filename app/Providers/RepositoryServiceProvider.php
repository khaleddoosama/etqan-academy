<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->bindRepositories();
        $this->bindFolderRepositories();
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

    protected function bindFolderRepositories()
    {
        $folders = File::directories(app_path('Repositories/Contracts'));

        foreach ($folders as $folder) {
            $folderName = pathinfo($folder, PATHINFO_FILENAME);

            $contractsPath = app_path('Repositories/Contracts/' . $folderName);
            $eloquentPath = app_path('Repositories/Eloquent/' . $folderName);

            $interfaces = File::allFiles($contractsPath);

            foreach ($interfaces as $interface) {
                $interfaceName = pathinfo($interface->getFilename(), PATHINFO_FILENAME);
                $modelName = str_replace('RepositoryInterface', '', $interfaceName);

                $interfaceClass = "App\\Repositories\\Contracts\\{$folderName}\\{$interfaceName}";
                $repositoryClass = "App\\Repositories\\Eloquent\\{$folderName}\\{$modelName}Repository";

                if (class_exists($repositoryClass)) {
                    $this->app->bind($interfaceClass, $repositoryClass);
                }
            }
        }
    }

    public function boot()
    {
        //
    }
}
