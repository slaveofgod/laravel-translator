<?php

namespace AB\Laravel\Translator\Services;

use Symfony\Component\Finder\Finder;

/**
 * @author Alexey Bob <alexey.bob@gmail.com>
 */
class AppService
{
    public function getLanguages()
    {
        $languages = [];
        
        $finder = new Finder();

        // Files
        foreach ($finder->files()->in(resource_path('lang'))->depth('== 0') as $file) {
            
            $languages[$file->getBasename('.' . $file->getExtension())] = [
                'locale' => $file->getRelativePathname(),
                'name' => \Locale::getDisplayLanguage($file->getBasename('.' . $file->getExtension()), \App::getLocale()),
                'country' => locale_country($file->getBasename('.' . $file->getExtension()))
            ];
        }
        
        // Directories
        foreach ($finder->directories()->in(resource_path('lang'))->exclude('backup') as $file) {
            $languages[$file->getRelativePathname()] = [
                'locale' => $file->getRelativePathname(),
                'name' => \Locale::getDisplayLanguage($file->getRelativePathname(), \App::getLocale()),
                'country' => locale_country($file->getRelativePathname())
            ];
        }
        
        return $languages;
    }
}