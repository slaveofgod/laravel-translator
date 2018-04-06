<?php

namespace Translator\Facades;

use Illuminate\Filesystem\Filesystem;


/**
 * @author Alexey Bob <alexey.bob@gmail.com>
 */
class ResourceFacade
{
    private $path;
    
    private $format;
    
    private $backupName;
    
    private $isModified = false;
    
    private $messages = [];
    
    private $newMessages = [];
    
    
    
    public function __construct($path, $format, $backupName)
    {
        $this->path = $path;
        $this->format = $format;
        $this->backupName = $backupName;
        
        $this->messages = $this->getContents();
    }
    
    public function getContents()
    {
        $contents = [];
        
        try {
            switch ($this->format) {
                case 'json':
                    $contents = json_decode((new Filesystem)->get($this->path), true);
                    break;

                case 'php':
                    $contents = include($this->path);
                    break;
            }
        } catch (\Exception $ex) {}
        
        return $contents;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function getFileBaseName()
    {
        return pathinfo($this->path,  PATHINFO_FILENAME);
    }
    
    public function isFileExist()
    {
        return (new Filesystem)->isFile($this->path);
    }
    
    public function getRelativePathname()
    {
        return str_replace(resource_path() . '/', "", $this->path);
    }
    
    public function getLocalPathname()
    {
        return str_replace(resource_path() . '/lang/', "", $this->path);
    }
    
    public function getTranslations()
    {
        $contents = $this->getContents();
        $translations = [];
        
        foreach ($contents as $key => $value) {
            $key = ('json' !== $this->format) ? ($this->getFileBaseName() . '.' . $key) : $key;
            if (!is_array($value)) {
                $translations[$key] = $value; 
            } else {
                /**
                 * @todo Process multi-dimensional arrays: "key1.key2.key3: value"
                 */
//                array_walk_recursive($contents, function ($item, $key) {
//                    echo "$key | $item\n";
//                });
            }
        }
        
        return $translations;
    }
    
    public function addMessage($message, $value = "")
    {
        $this->messages[$message] = $value;
        
        $this->newMessages[] = array($message);
        
        $this->isModified = true;
    }
    
    public function deleteMessage($message)
    {
        if (isset($this->messages[$message])) {
            unset($this->messages[$message]);
            $this->isModified = true;
        }
    }
    
    public function save($noBackup = false)
    {
        if (
            true === $this->isModified
            && true === $this->isFileExist()
            && false === $noBackup
        ) {
            $this->backup();
        }
        
        if (true === $this->isModified) {
            if (false === \File::isDirectory(dirname($this->path))) {
                \File::makeDirectory(dirname($this->path), 0755, true);
            }
            
            switch ($this->format) {
                case 'json':
                    \File::put($this->path, json_encode($this->messages, JSON_PRETTY_PRINT));
                    break;
                
                case 'php':
                    \File::put($this->path, "<?php\n\nreturn " . var_export($this->messages, true) . ";");
                    break;
            }
        }
    }
    
    private function backup()
    {
        $filePath = resource_path('lang/backup/' . $this->backupName . '/' . $this->getLocalPathname());
        if (false === \File::isDirectory(dirname($filePath))) {
            \File::makeDirectory(dirname($filePath), 0755, true);
        }
        \File::copy($this->path, $filePath);
    }
    
    public function isModified()
    {
        return $this->isModified;
    }
    
    public function hasNewMessages()
    {
        return (count($this->newMessages) > 0) ? true : false;
    }
    
    public function getNewMessages()
    {
        return $this->newMessages;
    }
    
    public function getMessages()
    {
        return $this->messages;
    }
}