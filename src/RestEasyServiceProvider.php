<?php
namespace Chriscalifornia88\RestEasy;

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

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            [
                RestControllerMakeCommand::class,
            ]
        );
    }
}
