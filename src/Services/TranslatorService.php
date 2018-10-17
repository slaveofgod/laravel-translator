<?php

namespace AB\Laravel\Translator\Services;

use Symfony\Component\Finder\Finder; // https://symfony.com/doc/current/components/finder.html
use Illuminate\Filesystem\Filesystem;
use AB\Laravel\Translator\Facades\ResourceFacade;


/**
 * @author Alexey Bob <alexey.bob@gmail.com>
 */
class TranslatorService
{
    use \AB\Laravel\Translator\Traits\ResourceTrait;
    
    use \AB\Laravel\Translator\Traits\MessageTrait;
    
    /**
     *
     * @var string
     */
    private $locale;
    
    /**
     *
     * @var string
     */
    private $resource_path;

    /**
     *
     * @var string
     */
    private $template_path;
    
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
     * @param string $resource_path
     * @param string $template_path
     * @param string $prefixes
     * @param bool $dev
     * @param string $backupName
     * @param string $extract
     */
    public function __construct(
        string $locale,
        string $resource_path = null,
        string $template_path = null,
        string $prefixes = null,
        bool $dev = false,
        string $backupName = null,
        string $extract = 'view'
    ) {
        $this->locale = $locale;
        $this->resource_path = $resource_path;
        $this->template_path = $template_path;
        $this->prefixes = $prefixes;
        $this->dev = $dev;
//        $this->backupName = (null !== $backupName) ? $backupName : date('Y-m-d\TH:i:s');
        $this->backupName = (null !== $backupName) ? $backupName : date('U');
        
        $this->loadResources();
        if ('view' === $extract) {
            $this->extractViewMessages();
        } else {
            $this->extractUntrackedMessages();
        }
    }
    
    /**
     * @return void
     */
    public function clean()
    {
        foreach ($this->resources as $resource) {
            
            if (false === in_array($resource->getFileBaseName(), ['validation', 'auth', 'passwords', 'pagination'])) {
                foreach ($resource->getTranslations() as $key => $value) {
                    if (false === $this->hasMessage($key)) {
                        $resource->deleteMessage($this->cleanMessage($key));
                    }
                }
            }
        }
    }
    
    /**
     * 
     * @param boolean $noBackup
     * 
     * @return void
     */
    public function save(bool $noBackup = false)
    {
        foreach ($this->resources as $resource) {
            $resource->save($noBackup);
        }
    }
    
    /**
     * 
     * @return integer
     */
    public function getUntranslatedMessagesCount()
    {
        $count = 0;
        foreach ($this->resources as $resource) {
            $count += count($resource->getUntranslatedMessages());
        }
        
        return $count;
    }
    
    /**
     * 
     * @return integer
     */
    public function getMessagesCount()
    {
        $count = 0;
        foreach ($this->resources as $resource) {
            $count += count($resource->getMessages());
        }
        
        return $count;
    }
    
    /**
     * 
     * @param string $hesh
     * 
     * @return ResourceFacade
     */
    public function getResourceByHesh($hesh)
    {
        foreach ($this->resources as $resource) {
            if ($hesh === md5($resource->getRelativePathname())) {
                return $resource;
            }
        }
        
        return null;
    }
}