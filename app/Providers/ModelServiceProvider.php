<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
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
        $this->app->bind(
            \App\Models\AlbumRepository::class,
            \App\Persistence\AlbumDbRepository::class
        );

        $this->app->bind(
            \App\Models\ArtistRepository::class,
            \App\Persistence\AlbumDbRepository::class
        );

        $this->app->bind(
            \App\Models\DiscographyRepository::class,
            \App\Persistence\AlbumDbRepository::class
        );
    }
}
