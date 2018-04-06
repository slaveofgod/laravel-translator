<?php

namespace Translator\Services;

use Symfony\Component\Finder\Finder; // https://symfony.com/doc/current/components/finder.html
use Illuminate\Filesystem\Filesystem;
use Translator\Facades\ResourceFacade;


/**
 * @author Alexey Bob <alexey.bob@gmail.com>
 */
class TranslatorService
{
    private $locale;
    
    private $path;
    
    private $prefixes;
    
    private $backupName;
    
    private $resources = [];
    
    private $messages = [];
    
    
    
    public function __construct($locale, $path, $prefixes, $backupName = null)
    {
        $this->locale = $locale;
        $this->path = $path;
        $this->prefixes = $prefixes;
        $this->backupName = (null !== $backupName) ? $backupName : date('Y-m-d\TH:i:s');
        
        $this->loadResources();
        $this->extractMessages();
    }
    
    private function loadResources()
    {
        /**
         * resources/lang/{$locale}.json
         */
        try {
            $path = resource_path('lang/' . $this->locale . '.json');
            $content = (new Filesystem)->get($path);
            
            $this->addResource($path, 'json');
        } catch (\Exception $ex) {}
        
        
        /**
         * resources/lang/{$locale}/*.php
         */
        try {
            $path = resource_path('lang/' . $this->locale . '/');
            $finder = new Finder();
            $finder->files()->name('*.php')->in($path);
            foreach ($finder as $file) {
                $this->addResource($file->getRealPath(), 'php');
            }
        } catch (\Exception $ex) {}
    }
    
    private function addResource($path, $format)
    {
        $resource = new ResourceFacade($path, $format, $this->backupName);
        $this->resources[] = $resource;
        
        return $resource;
    }
    
    private function extractMessages()
    {
        $finder = new Finder();
        $finder->files()->name('*.blade.php')->in($this->path);
        foreach ($finder as $file) {
            $contents = $file->getContents();
            $prefixes = explode(',', $this->prefixes);
            foreach ($prefixes as $prefix) {
                $this->extractMessagesFromContents($contents, $prefix);
            }
        }
        
        usort($this->messages, function ($a, $b) {
            return ($a['count'] <  $b['count']);
        });
    }
    
    private function extractMessagesFromContents($contents, $prefix)
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
                                'value' => $value[1],
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
    
    private function getNewMessages()
    {
        $translations = [];
        $newMessages = [];
        
        foreach ($this->resources as $resource) {
            $translations = array_merge($translations, $resource->getTranslations());
        }
     
        foreach ($this->messages as $message) {
            if (!isset($translations[$message['value']])) {
                $newMessages[] = $message['value'];
            }
        }
        
        return $newMessages;
    }
    
    public function getMessages()
    {
        return $this->messages;
    }
    
    public function getMessage($key)
    {
        foreach ($this->messages as $message) {
            if ($key === $message['value']) {
                return $message;
            }
        }
        
        return null;
    }
    
    public function hasMessage($key)
    {
        return (null !== $this->getMessage($key)) ? true : false;
    }
    
    public function addNewMessages()
    {
        foreach ($this->getNewMessages() as $message) {
            $filePath = resource_path('lang/' . $this->locale . '.json');
            if (preg_match('/(^[a-zA-Z0-9]+)\.([\S].*)/', $message, $match)) {
                $filePath = resource_path('lang/' . $this->locale . '/' . $match[1] . '.php');
                $message = $match[2];
            }
            
            $resource = $this->getOrLoadResource($filePath);
            
            $resource->addMessage($message);
        }
    }
    
    private function cleanMessage($message)
    {
        if (preg_match('/(^[a-zA-Z0-9]+)\.([\S].*)/', $message, $match)) {
            $message = $match[2];
        }
        
        return $message;
    }
    
    public function clean()
    {
        foreach ($this->resources as $resource) {
            foreach ($resource->getTranslations() as $key => $value) {
                if (false === $this->hasMessage($key)) {
                    $resource->deleteMessage($this->cleanMessage($key));
                }
            }
        }
    }
    
    public function save($noBackup = false)
    {
        foreach ($this->resources as $resource) {
            $resource->save($noBackup);
        }
    }
    
    private function getOrLoadResource($filePath)
    {
        $resource = $this->findResourceByFilePath($filePath);
        if (null === $resource) {
            $resource = $this->addResource($filePath, pathinfo($filePath,  PATHINFO_EXTENSION));
        }
        
        return $resource;
    }
    
    private function findResourceByFilePath($filePath)
    {
        foreach ($this->resources as $resource) {
            if ($filePath === $resource->getPath()) {
                return $resource;
            }
        }
        
        return null;
    }
    
    public function getResources()
    {
        return $this->resources;
    }
}