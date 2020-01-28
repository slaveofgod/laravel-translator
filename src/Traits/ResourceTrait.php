<?php

namespace AB\Laravel\Translator\Traits;

use Illuminate\Filesystem\Filesystem;
use AB\Laravel\Translator\Facades\ResourceFacade;
use Symfony\Component\Finder\Finder;


/**
 * @author Slave of God <iamtheslaveofgod@gmail.com>
 */
trait ResourceTrait
{
    /**
     * 
     * @return array
     */
    public function getResources() : array
    {
        return $this->resources;
    }
    
    /**
     * 
     * @param string $path
     * @param string $format
     * 
     * @return ResourceFacade
     */
    private function addResource(string $path, string $format) : ResourceFacade
    {
        $resource = new ResourceFacade($this->resource_path, $path, $format, $this->backupName);
        $this->resources[] = $resource;
        
        return $resource;
    }
    
    /**
     * 
     */
    private function loadResources()
    {
        /**
         * resources/lang/{$locale}.json
         */
        try {
            $path = $this->resource_path . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $this->locale . '.json';
            $content = (new Filesystem)->get($path);
            
            $this->addResource($path, 'json');
        } catch (\Exception $ex) {}
        
        
        /**
         * resources/lang/{$locale}/*.php
         */
        try {
            $path = $this->resource_path . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $this->locale . DIRECTORY_SEPARATOR;
            $finder = new Finder();
            $finder->files()->name('*.php')->in($path);
            foreach ($finder as $file) {
                $this->addResource($file->getRealPath(), 'php');
            }
        } catch (\Exception $ex) {}
    }
    
    /**
     * 
     * @param string $filePath
     * 
     * @return ResourceFacade
     */
    private function getOrLoadResource(string $filePath) : ResourceFacade
    {
        $resource = $this->findResourceByFilePath($filePath);
        if (null === $resource) {
            $resource = $this->addResource($filePath, pathinfo($filePath,  PATHINFO_EXTENSION));
        }
        
        return $resource;
    }
    
    /**
     * 
     * @param string $filePath
     * 
     * @return ResourceFacade
     */
    private function findResourceByFilePath(string $filePath)
    {
        foreach ($this->resources as $resource) {
            if ($filePath === $resource->getPath()) {
                return $resource;
            }
        }
        
        return null;
    }
}