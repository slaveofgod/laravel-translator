<?php

namespace Translator\Traits;

use Illuminate\Filesystem\Filesystem;
use Translator\Facades\ResourceFacade;
use Symfony\Component\Finder\Finder;


/**
 * @author Alexey Bob <alexey.bob@gmail.com>
 */
trait ResourceTrait
{
    /**
     * 
     * @return array
     */
    public function getResources()
    {
        return $this->resources;
    }
    
    /**
     * 
     * @param string $path
     * @param string $format
     * @return ResourceFacade
     */
    private function addResource($path, $format)
    {
        $resource = new ResourceFacade($path, $format, $this->backupName);
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
    
    /**
     * 
     * @param string $filePath
     * @return ResourceFacade
     */
    private function getOrLoadResource($filePath)
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
     * @return ResourceFacade
     */
    private function findResourceByFilePath($filePath)
    {
        foreach ($this->resources as $resource) {
            if ($filePath === $resource->getPath()) {
                return $resource;
            }
        }
        
        return null;
    }
}