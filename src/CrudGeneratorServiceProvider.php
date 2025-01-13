<?php

namespace Shafarizkyf\CrudGenerator;

use Illuminate\Support\ServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Load the command
        $this->commands([
            \Shafarizkyf\CrudGenerator\Commands\MakeCrud::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the stub files
        $this->publishes([
            __DIR__ . '/stubs' => base_path('stubs/crud-generator'),
        ], 'crud-generator-stubs');
    }
}
