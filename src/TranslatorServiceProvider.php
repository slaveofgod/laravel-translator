<?php

namespace Translator;

use Illuminate\Support\ServiceProvider;
use Translator\Validation\Rules\LocaleValidationRule;


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
class TranslatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setConfig();
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Translator\Console\TranslationUpdateCommand::class,
                \Translator\Console\TranslationDiffCommand::class,
                \Translator\Console\TranslationUntrackedCommand::class,
            ]);
            
            $this->validationRules();
        }
    }
    
    /**
     * 
     */
    private function setConfig() : void
    {
        \Config::set('translator_log', 'translator.log');
    }
    
    /**
     * 
     */
    private function validationRules() : void
    {
        \Validator::extend('locale', function($attribute, $value, $parameters, $validator) {

            $rule = new LocaleValidationRule();
            
            if (false === $rule->passes($attribute, $value)) {
                
                return $rule->message();
            }
            
            return false;
        });
    }
}