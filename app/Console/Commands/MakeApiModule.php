<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeApiModule extends Command
{

    protected $signature = 'make:module {name}';
    protected $description = 'Generate full API module (model, migration, controller, request, resource, service, route)';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $plural = Str::pluralStudly($name);
        $kebabPlural = Str::kebab($plural);

        // 1. Generate essentials
        $this->components->info("📦 Generating $name Module...");

        $commands = [
            ['make:model', ['name' => $name, '--migration' => true]],
            ['make:controller', ['name' => "Api/{$name}Controller", '--api' => true]],
            ['make:request', ['name' => "{$name}Request"]],
            ['make:resource', ['name' => "{$name}Resource"]],
        ];

        foreach ($commands as [$command, $params]) {
            $this->callSilent($command, $params);
        }

        $this->generateService($name);
        $this->generateController($name);
        $this->appendRoute($name, $kebabPlural);

        $this->components->info("✅ Module $name created successfully.");
    }

    protected function generateService(string $name)
    {
        $servicePath = app_path("Services/{$name}Service.php");
        $model = $name;

        if (!File::exists($servicePath)) {
            File::ensureDirectoryExists(app_path('Services'));

            File::put($servicePath, <<<PHP
    <?php

    namespace App\Services;

    use App\Models\\{$model};

    class {$name}Service
    {
        public function getAll()
        {
            return {$model}::latest()->get();
        }

        public function store(array \$data)
        {
            return {$model}::create(\$data);
        }
    }
    PHP);
            $this->info("🛠️ Service created: Services/{$name}Service.php");
        }
    }

    protected function generateController(string $name)
    {
        $controllerPath = app_path("Http/Controllers/Api/{$name}Controller.php");

        $resource = "{$name}Resource";
        $request = "Api\{$name}Request";
        $service = "{$name}Service";
        $modelVar = Str::camel($name);
        $serviceVar = Str::camel($service);

        File::put($controllerPath, <<<PHP
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\\{$request};
use App\Http\Resources\\{$resource};
use App\Services\\{$service};

class {$name}Controller extends Controller
{

    use ApiResponseTrait;

    protected \${$serviceVar};

    public function __construct({$service} \${$serviceVar})
    {
        \$this->{$serviceVar} = \${$serviceVar};
    }

    public function index()
    {
        \$items = \$this->{$serviceVar}->getAll();
        return \$this->apiResponse({$resource}::collection(\$items), 'ok', 200);
    }

    public function store({$request} \$request)
    {
        \$item = \$this->{$serviceVar}->store(\$request->validated());
        return \$this->apiResponse(new {$resource}(\$item), 'ok', 201);
    }
}
PHP);

        $this->info("🎮 Controller updated with service & request logic.");
    }



    protected function appendRoute(string $name, string $routeName)
    {
        $controller = "App\Http\Controllers\Api\\{$name}Controller";
        $routeDefinition = <<<PHP

// Auto-generated for {$name}
Route::apiResource('{$routeName}', \\{$controller}::class);
PHP;

        File::append(base_path('routes/api.php'), $routeDefinition);
        $this->info("🛣️ Route added to routes/api.php");
    }
}
