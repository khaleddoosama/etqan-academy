<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name}';
    protected $description = 'Create a Repository and Interface for a Model';

    public function handle()
    {
        $name = $this->argument('name');
        $interfaceName = "{$name}RepositoryInterface";
        $repositoryName = "{$name}Repository";

        $interfacePath = app_path("Repositories/Contracts/{$interfaceName}.php");
        $repositoryPath = app_path("Repositories/Eloquent/{$repositoryName}.php");

        // Create directories if not exist
        if (!File::exists(app_path('Repositories/Contracts'))) {
            File::makeDirectory(app_path('Repositories/Contracts'), 0755, true);
        }
        if (!File::exists(app_path('Repositories/Eloquent'))) {
            File::makeDirectory(app_path('Repositories/Eloquent'), 0755, true);
        }

        // Create Interface
        File::put($interfacePath, $this->getInterfaceTemplate($interfaceName));

        // Create Repository
        File::put($repositoryPath, $this->getRepositoryTemplate($name, $repositoryName, $interfaceName));

        $this->info("Repository and Interface for {$name} created successfully!");

        return 0;
    }

    protected function getInterfaceTemplate($interfaceName)
    {
        return <<<EOT
<?php

namespace App\Repositories\Contracts;

interface {$interfaceName}
{
    //
}
EOT;
    }

    protected function getRepositoryTemplate($modelName, $repositoryName, $interfaceName)
    {
        return <<<EOT
<?php

namespace App\Repositories\Eloquent;

use App\Models\\{$modelName};
use App\Repositories\Contracts\\{$interfaceName};

class {$repositoryName} extends BaseRepository implements {$interfaceName}
{
    protected function model()
    {
        return new {$modelName}();
    }
}
EOT;
    }
}
