<?php

namespace AB\Laravel\Translator;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use AB\Laravel\Translator\Validation\Rules\LocaleValidationRule;
use Illuminate\Support\Facades\Blade;


/**
 * @todo __(), _n(), $translator->trans('Hello World'), {{ trans('Hello’) }} and trans_choice() 
 * 
 * [![Latest Stable Version](https://poser.pugx.org/Lecturize/Laravel-Translatable/v/stable)](https://packagist.org/packages/Lecturize/Laravel-Translatable)
 * [![Total Downloads](https://poser.pugx.org/Lecturize/Laravel-Translatable/downloads)](https://packagist.org/packages/Lecturize/Laravel-Translatable)
 * [![License](https://poser.pugx.org/Lecturize/Laravel-Translatable/license)](https://packagist.org/packages/Lecturize/Laravel-Translatable)
 */


/**
 * @author Alexey Bob <alexey.bob@gmail.com>
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Config
        $this->setConfig();
        
        // Laravel Blade Directives
        $this->setBladeDirectives();
        
        // Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \AB\Laravel\Translator\Console\TranslationUpdateCommand::class,
                \AB\Laravel\Translator\Console\TranslationDiffCommand::class,
                \AB\Laravel\Translator\Console\TranslationUntrackedCommand::class,
            ]);
            
//            $this->validationRules();
        }
        
        // Views
        $this->loadViewsFrom(__DIR__.'/Resources/views', 'ABTranslator');
        
        // Routes
        $this->loadRoutesFrom(__DIR__.'/Resources/routes/web.php');
        
        // Public Assets
        // php artisan vendor:publish --tag=abtranslator --force
        $this->publishes([
            __DIR__.'/Resources/public/skins' => public_path('vendor/abtranslator'),
        ], 'abtranslator');
        
        // Translations
        $this->loadTranslationsFrom(__DIR__.'/Resources/lang', 'abtranslator');
    }
    
    /**
     * @return void
     */
    private function setConfig()
    {
        \Config::set('translator_log', 'translator.log');
        \Config::set('resource_path', __DIR__ . '/Resources/test');
    }
    
//    /**
//     * @return void
//     */
//    private function validationRules()
//    {
//        \Validator::extend('locale', function($attribute, $value, $parameters, $validator) {
//
//            $rule = new LocaleValidationRule();
//
//            if (true === $rule->passes($attribute, $value)) {
//                
//                return true;
//            }
//            
//            return false;
//        });
//    }
    
    /**
     * @link https://laravel.com/docs/5.5/blade#extending-blade
     * 
     * @return void
     */
    private function setBladeDirectives()
    {
        Blade::directive('lang_ab', function ($expression) {
            return "<?php echo __ab({$expression}); ?>";
        });
        
        Blade::directive('choice_ab', function ($expression) {
            return "<?php echo trans_choice_ab({$expression}); ?>";
        });
    }
}