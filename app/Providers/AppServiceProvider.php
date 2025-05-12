<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenAI;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //Binds the OpenAI client to the service container
        // This allows you to use the OpenAI client throughout your application
        // without needing to create a new instance each time.
        $this->app->singleton(\OpenAI\Client::class, function () {
            return OpenAI::client(env('OPENAI_API_KEY'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }


}
