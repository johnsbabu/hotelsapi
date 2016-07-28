<?php

namespace Techions\Hotelapi;

use Illuminate\Support\ServiceProvider;

class ChannelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        
        include __DIR__.'/routes.php';
        $this->app->make('Techions\Hotelapi\Hotels\Channel');

    }
}
