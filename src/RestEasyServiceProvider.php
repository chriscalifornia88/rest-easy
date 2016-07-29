<?php
namespace Chriscalifornia88\RestEasy;

use Chriscalifornia88\RestEasy\Commands\RestControllerMakeCommand;
use Illuminate\Support\ServiceProvider;

/**
 * User: Christian Augustine
 * Date: 7/28/16
 * Time: 8:21 PM
 */
class RestEasyServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    protected $commands = [
        'command.rest-controller.make'
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.rest-controller.make'];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['command.rest-controller.make'] = $this->app->share(
            function () {
                return new RestControllerMakeCommand();
            }
        );

        $this->commands($this->commands);
    }
}
