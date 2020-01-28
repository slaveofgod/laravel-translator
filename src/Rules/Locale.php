<?php

namespace AB\Laravel\Translator\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Filesystem\Filesystem;


/**
 * @author Slave of God <iamtheslaveofgod@gmail.com>
 */
class Locale implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * 
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Storage' . DIRECTORY_SEPARATOR . 'locales.json';
            $content = json_decode((new Filesystem)->get($path), true);
            
            return (true === array_key_exists($value, $content));
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('abtLang::validation.locale');
    }
}