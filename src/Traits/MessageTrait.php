<?php

namespace AB\Laravel\Translator\Traits;

use Symfony\Component\Finder\Finder;
use Illuminate\Filesystem\Filesystem;


/**
 * @author Slave of God <iamtheslaveofgod@gmail.com>
 */
trait MessageTrait
{
    /**
     * 
     * @return array
     */
    public function getMessages() : array
    {
        return $this->messages;
    }
    
    /**
     * 
     * @param string $key
     * 
     * @return mixed
     */
    public function getMessage(string $key)
    {
        foreach ($this->messages as $message) {
            if ($key === $message['value']) {
                return $message;
            }
        }
        
        return null;
    }
    
    /**
     * 
     * @param string $key
     * 
     * @return boolean
     */
    public function hasMessage(string $key) : bool
    {
        return (null !== $this->getMessage($key)) ? true : false;
    }
    
    /**
     * 
     * @param string $message
     * 
     * @return string
     */
    private function cleanMessage(string $message) : string
    {
        if (preg_match('/(^[a-zA-Z0-9]+)\.([\S].*)/', $message, $match)) {
            $message = $match[2];
        }
        
        return $message;
    }
    
    /**
     * 
     * @param boolean $tableView
     * 
     * @return array
     */
    public function getNewMessages(bool $tableView = false) : array
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
        
        return (false === $tableView) ? $newMessages : $this->tableView($newMessages);
    }
    
    /**
     * @return void
     */
    public function addNewMessages()
    {
        foreach ($this->getNewMessages() as $message) {
            $filePath = $this->resource_path . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $this->locale . '.json';
            if (preg_match('/(^[a-zA-Z0-9]+)\.([\S].*)/', $message, $match)) {
                $filePath = $this->resource_path . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $this->locale . DIRECTORY_SEPARATOR . $match[1] . '.php';
                $message = $match[2];
            }
            
            $resource = $this->getOrLoadResource($filePath);
            
            $resource->addMessage($message);
        }
    }
    
    /**
     * @return void
     */
    private function extractViewMessages()
    {
        $finder = new Finder();
        $finder->files()->name('*.blade.php')->in($this->template_path);
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
    
    /**
     * @return void
     */
    private function extractUntrackedMessages()
    {
        $contents = [];
        try {
            $contents = explode(PHP_EOL, (new Filesystem)->get(base_path(sprintf('storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . '%s', \Config::get('translator_log')))));
        } catch (\Exception $ex) {}
        
        foreach ($contents as $content) {
            try {
                $message = json_decode($content, true);
                if ($this->locale === $message['locale']) {
                    if (array_key_exists($message['message'], $this->messages)) {
                        $this->messages[$message['message']]['count'] ++;
                    } else {
                        $this->messages[$message['message']] = array(
                            'value' => $message['message'],
                            'count' => 1
                        );
                    }
                }
            } catch (\Exception $ex) {}
        }
        
        usort($this->messages, function ($a, $b) {
            return ($a['count'] <  $b['count']);
        });
    }
    
    /**
     * 
     * @param string $contents
     * @param string $prefix
     * 
     * @return void
     */
    private function extractMessagesFromContents(string $contents, string $prefix)
    {
        $this->extractMessagesFromContentsByPattern('/' . $prefix . '\(\s*\'([^\']+)\'[^)]*\)/i', $contents);
        $this->extractMessagesFromContentsByPattern('/' . $prefix . '\(\s*\"([^"]+)\"[^)]*\)/i', $contents);
    }
    
    /**
     * 
     * @param string $pattern
     * @param string $contents
     * 
     * @return void
     */
    private function extractMessagesFromContentsByPattern(string $pattern, string $contents)
    {
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
    }

    /**
     * 
     * @param array $data
     * 
     * @return array
     */
    private function tableView(array $data) : array
    {
        foreach ($data as $key => $value) {
            if (false === is_array($value)) {
                $data[$key] = array('value' => $value);
            }
        }
        
        return $data;
    }
}