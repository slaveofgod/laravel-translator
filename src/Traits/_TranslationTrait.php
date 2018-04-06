<?php

namespace Translator\Traits;


/**
 * @author Alexey Bob <alexey.bob@gmail.com>
 */
trait TranslationTrait
{
    private function loadAnyMessagesFromTemplates($contents, $prefix)
    {
        $parser = function ($pattern, $contents) {
            preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER, 0);
            if (count( $matches ) > 0) {
                foreach ($matches as $key => $value) {
                    if (isset($value[1])) {
                        if (array_key_exists($value[1], $this->messages)) {
                            $this->messages[$value[1]]['count'] ++;
                        } else {
                            $this->messages[$value[1]] = array(
                                'message' => $value[1],
                                'count' => 1
                            );
                        }
                    }
                }
            }
        };
        
        $parser('/' . $prefix . '\(\s*\'([^\']+)\'[^)]*\)/i', $contents);
        
        $parser('/' . $prefix . '\(\s*\"([^"]+)\"[^)]*\)/i', $contents);
    }
    
    private function getProcessedTranslations($clean = false)
    {
        $this->nonexistentTranslations = $this->translations;
        foreach ($this->messages as $message) {
            if(isset($this->nonexistentTranslations[$message['message']])) {
                unset($this->nonexistentTranslations[$message['message']]);
                
            }
        }
        
        foreach ($this->messages as $message) {
            if(isset($this->translations[$message['message']])) {
                $this->existingTranslations[$message['message']] = $this->translations[$message['message']];
            }
        }

        foreach ($this->messages as $message) {
            if (false === isset($this->translations[$message['message']])) {
                $this->newTranslations[$message['message']] = '';
            }
        }
        
        if ( true === $clean ) {
            return array_merge($this->existingTranslations, $this->newTranslations);
        } else {
            return array_merge($this->nonexistentTranslations, $this->existingTranslations, $this->newTranslations);
        }
    }
    
    private function getFilePath($absolute_path = true)
    {
        $filePath = '';
        
        if ($this->option('domain')) {
            $filePath .= $this->argument('locale') . '/' . $this->option('domain') . '.' . $this->option('output-format');
            if (false === \File::isDirectory(dirname(resource_path('lang/' . $filePath)))) {
                \File::makeDirectory(dirname(resource_path('lang/' . $filePath)), 0755, true);
            }
        } else {
            $filePath .= $this->argument('locale') . '.' . $this->option('output-format');
        }
        
        if (true === $absolute_path) {
            return resource_path('lang/' . $filePath);
        } else {
            return $filePath;
        }
    }
    
    private function backupFile()
    {
        $backupFilePath = resource_path('lang/backup/' . date('Y-m-d\TH:i:s') . '/' . $this->getFilePath(false));
        
        if (false === $this->option('no-backup') && count($this->translations) > 0) {
            if (false === \File::isDirectory(dirname($backupFilePath))) {
                \File::makeDirectory(dirname($backupFilePath), 0755, true);
            }
            \File::copy($this->getFilePath(), $backupFilePath);
        }
    }
}