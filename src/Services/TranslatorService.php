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
    use \Translator\Traits\ResourceTrait;
    use \Translator\Traits\MessageTrait;
    
    /**
     *
     * @var string
     */
    private $locale;
    
    /**
     *
     * @var string
     */
    private $path;
    
    /**
     *
     * @var array
     */
    private $prefixes;
    
    /**
     *
     * @var string
     */
    private $backupName;
    
    /**
     *
     * @var array
     */
    private $resources = [];
    
    /**
     *
     * @var array
     */
    private $messages = [];
    
    
    
    /**
     * 
     * @param string $locale
     * @param string $path
     * @param string $prefixes
     * @param string $backupName
     */
    public function __construct(string $locale, string $path, string $prefixes, string $backupName = null)
    {
        $this->locale = $locale;
        $this->path = $path;
        $this->prefixes = $prefixes;
        $this->backupName = (null !== $backupName) ? $backupName : date('Y-m-d\TH:i:s');
        
        $this->loadResources();
        $this->extractMessages();
    }
    
    /**
     * 
     */
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
    
    /**
     * 
     * @param boolean $noBackup
     */
    public function save(bool $noBackup = false)
    {
        foreach ($this->resources as $resource) {
            $resource->save($noBackup);
        }
    }
}