<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name : The name of the class}';

    protected $type = "Repository";
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    protected function getStub()
    {
        return __DIR__ . "/stubs/repository.stub";
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return base_path('app') . str_replace('\\', '/', $name) . '.php';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories';
    }

    protected function rootNamespace()
    {
        return "App";
    }
}
