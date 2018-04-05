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
        Log::info('[TranslatorLogger] ' . json_encode(array('message' => $key, 'locale' => app()->getLocale())));
        
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
        // Search translation in files from {locale} folder
        if (true === Lang::has($key)) {
            
            return trans_choice($key, $number, $replace, $locale);
        }
        
        // Save to log file when translation does not exist for this $key
        Log::info('[TranslatorLogger] ' . json_encode(array('message' => $key, 'locale' => app()->getLocale())));
        
        return trans_choice($key, $number, $replace, $locale);
    }
}