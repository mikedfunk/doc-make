<?php
/**
 * DocMake service provider. Used for defining IoC bindings, event
 * listeners, etc.
 *
 * @package DocMake
 * @license MIT
 */
namespace TerrenceHoward\DocMake;

use Illuminate\Support\ServiceProvider;

/**
 * DocMakeServiceProvider
 *
 * @author Michael Funk <mike.funk@internetbrands.com>
 */
class DocMakeServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

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
        // register command
        $this->app->bind(
            'doc.make',
            'TerrenceHoward\DocMake\Commands\DocMakeCommand'
        );
        $this->commands('doc.make');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
