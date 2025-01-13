<?php

namespace Shafarizkyf\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {name} {--controller-path= : Specify the subdirectory for the controller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a model, migration, and controller with CRUD operations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $modelName = Str::studly($name);

        $controllerPath = $this->option('controller-path');
        $controllerNamespace = $controllerPath ? 'App\\Http\\Controllers\\' . str_replace('/', '\\', $controllerPath) : 'App\\Http\\Controllers';

        // Create the model with a migration
        $this->call('make:model', [
            'name' => $modelName,
            '--migration' => true,
        ]);

        // Build the controller name and file path
        $controllerName = "{$modelName}Controller";
        $controllerDirectory = app_path('Http/Controllers' . ($controllerPath ? '/' . $controllerPath : ''));
        $controllerFilePath = "{$controllerDirectory}/{$controllerName}.php";

        // Create the directory if it doesn't exist
        if (!File::exists($controllerDirectory)) {
            File::makeDirectory($controllerDirectory, 0755, true);
        }

        // Create the controller file
        $this->createControllerFile($controllerFilePath, $modelName, $controllerNamespace);

        $this->info("Model, migration, and controller for {$modelName} created successfully!");
        return 0;
    }

    /**
     * Add CRUD methods to the controller.
     *
     * @param string $modelName
     * @return void
     */
    protected function createControllerFile($filePath, $modelName, $namespace)
    {

        $stub = file_get_contents(__DIR__ . '/../stubs/crud-controller.stub');

        $stub = str_replace(
            ['{{modelName}}', '{{modelVariable}}', '{{namespace}}'],
            [$modelName, lcfirst($modelName), $namespace],
            $stub
        );

        // Include Controller import if not in the default namespace
        if ($namespace !== 'App\\Http\\Controllers') {
            $stub = str_replace(
                '{{useController}}',
                "use App\Http\Controllers\Controller;",
                $stub
            );
        } else {
            $stub = str_replace('{{useController}}', '', $stub);
        }

        file_put_contents($filePath, $stub);
    }
}
