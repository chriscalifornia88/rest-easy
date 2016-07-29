<?php
namespace Chriscalifornia88\RestEasy\Commands;

/**
 * User: Christian Augustine
 * Date: 7/28/16
 * Time: 8:27 PM
 */
class RestControllerMakeCommand extends \Illuminate\Console\GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:rest-controller {name} {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Rest Easy controller';

    /** @var string */
    private $model;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/rest-controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
        ];
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $namespace = $this->getNamespace($name);

        return str_replace("use $namespace\Controller;\n", '', parent::buildClass($name));

        return str_replace("DummyModel", '', $this->model);
    }
}
