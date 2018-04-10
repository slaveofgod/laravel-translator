<?php

namespace Translator\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Filesystem\Filesystem;

class LocaleValidationRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $path = __DIR__ . '/../../Storage/locales.json';
            $content = json_decode((new Filesystem)->get($path), true);
            
            return (false === array_key_exists($value, $content));
        } catch (\Exception $ex) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.locale');
    }
}