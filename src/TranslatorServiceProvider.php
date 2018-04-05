<?php

namespace Translator;

use Illuminate\Support\ServiceProvider;

/**
 * @author Alexey Bob <alexey.bob@gmail.com>
 */
class TranslatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Translator\Console\TranslationUpdateCommand::class,
            ]);
        }
    }
}