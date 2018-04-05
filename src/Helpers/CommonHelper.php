<?php

use Illuminate\Filesystem\Filesystem;

if (! function_exists('__ab')) {
    /**
     * Translate the given message.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  string  $locale
     * @return string|array|null
     */
    function __ab($key, $replace = [], $locale = null)
    {
        // Search translation in files from {locale} folder
        if (true === Lang::has($key)) {
            
            return __($key, $replace, $locale);
        }
        
        // Search translation in {locale}.json file
        try {
            $content = json_decode((new Filesystem)->get(resource_path('lang/' . app()->getLocale() . '.json')), true);
            if (isset($content[$key])) {
                return __($key, $replace, $locale);
            }
        } catch (\Exception $ex) {}
        
        // Save to log file when translation does not exist for this $key
        
        Log::channel($key);
        
        dd($key);
        
        Log::channel('alexeybob/laravel-translator')->info($key);
        
        dd($key);
        
        dd(app('translator')->has($key, 'en'));
        
        if (true === app('translator')->has($key, 'en')) {
            
            return __($key, $replace, $locale);
        }
        
        dd($key);
        
        $line = __($key, [], $locale);
        
        // @todo Save to log file when translation does not exist for this $key
        if ($line === $key) {
            $locale = app()->getLocale();
            $message = $key;

            // Save to log file
        }
        
        return __($key, $replace, $locale);
    }
}

if (! function_exists('trans_choice_ab')) {
    /**
     * Translates the given message based on a count.
     *
     * @param  string  $key
     * @param  int|array|\Countable  $number
     * @param  array   $replace
     * @param  string  $locale
     * @return string
     */
    function trans_choice_ab($key, $number, array $replace = [], $locale = null)
    {
        $lineDefault = trans_choice($key, [], [], 'en');
        $line = trans_choice($key, [], [], $locale);
        
        dd($key, $lineDefault, $line);
        
        if ($line === $key) {
            $locale = app()->getLocale();
            $message = $key;
            
            dd($key, $line);
            
            // Save to log file
        }
        // @todo Save to log file when translation does not exist for this $key
        
        return trans_choice($key, $number, $replace, 'de');
    }
}