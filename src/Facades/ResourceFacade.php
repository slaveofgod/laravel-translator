<?php

namespace AB\Laravel\Translator\Facades;

use Illuminate\Filesystem\Filesystem;


/**
 * @author Slave of God <iamtheslaveofgod@gmail.com>
 */
class ResourceFacade
{
    /**
     *
     * @var string
     */
    private $resource_path;
    
    /**
     *
     * @var string
     */
    private $path;
    
    /**
     *
     * @var string
     */
    private $format;
    
    /**
     *
     * @var string
     */
    private $backupName;
    
    /**
     *
     * @var boolean
     */
    private $isModified = false;
    
    /**
     *
     * @var array
     */
    private $messages = [];
    
    /**
     *
     * @var array
     */
    private $newMessages = [];
    
    
    
    /**
     * 
     * @param type $resource_path
     * @param type $path
     * @param type $format
     * @param type $backupName
     */
    public function __construct($resource_path, $path, $format, $backupName)
    {
        $this->resource_path = $resource_path;
        $this->path = $path;
        $this->format = $format;
        $this->backupName = $backupName;
        
        $this->messages = $this->getContents();
    }
    
    /**
     * 
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
    
    /**
     * 
     * @return array
     */
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
    
    /**
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * 
     * @return string
     */
    public function getFileBaseName()
    {
        return pathinfo($this->path,  PATHINFO_FILENAME);
    }
    
    /**
     * 
     * @return boolean
     */
    public function isFileExist()
    {
        return (new Filesystem)->isFile($this->path);
    }
    
    /**
     * 
     * @return string
     */
    public function getRelativePathname()
    {
        return str_replace($this->resource_path . DIRECTORY_SEPARATOR, "", $this->path);
    }
    
    /**
     * 
     * @return string
     */
    public function getLocalPathname()
    {
        return str_replace($this->resource_path . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR, "", $this->path);
    }
    
    /**
     * 
     * @return array
     */
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
    
    /**
     * 
     * @param string $message
     * @param string $value
     * 
     * @return void
     */
    public function addMessage($message, $value = "")
    {
        $this->messages[$message] = $value;
        
        $this->newMessages[] = array($message);
        
        $this->isModified = true;
    }
    
    /**
     * 
     * @param string $message
     * @param string $value
     * 
     * @return bool
     */
    public function updateMessage($message, $value = "")
    {
        if (array_key_exists($message, $this->messages)) {
            $this->messages[$message] = $value;
            $this->isModified = true;
            
            return true;
        }
        
        return false;
    }
    
    /**
     * 
     * @param string $message
     * 
     * @return void
     */
    public function deleteMessage($message)
    {
        if (array_key_exists($message, $this->messages)) {
            unset($this->messages[$message]);
            $this->isModified = true;
        }
    }
    
    /**
     * 
     * @param string $hesh
     * @return array | null
     */
    public function getMessageByHesh($hesh)
    {
        foreach ($this->messages as $key => $value) {
            if ($hesh === md5($key)) {
                return [
                    'hesh' => md5($this->getRelativePathname()) . ',' . md5($key),
                    'original' => $key,
                    'translation' => $value
                ];
            }
        }
        
        return null;
    }
    
    /**
     * 
     * @param boolean $noBackup
     * 
     * @return void
     */
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
    
    /**
     * @return void
     */
    private function backup()
    {
        $filePath = $this->resource_path . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . $this->backupName . DIRECTORY_SEPARATOR . $this->getLocalPathname();
        if (false === \File::isDirectory(dirname($filePath))) {
            \File::makeDirectory(dirname($filePath), 0755, true);
        }
        \File::copy($this->path, $filePath);
    }
    
    /**
     * 
     * @return boolean
     */
    public function isModified()
    {
        return $this->isModified;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasNewMessages()
    {
        return (count($this->newMessages) > 0) ? true : false;
    }
    
    /**
     * 
     * @return string
     */
    public function getNewMessages()
    {
        return $this->newMessages;
    }
    
    /**
     * 
     * @return array
     */
    public function getUntranslatedMessages()
    {
        $untranslated = [];
        foreach ($this->messages as $key => $value) {
            if (!$value) {
                $untranslated[] = $key;
            }
        }
        
        return $untranslated;
    }
}