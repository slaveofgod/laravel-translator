<?php

namespace Translator\Console;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;


/**
 * A command that parses templates to extract translation messages and adds them
 * into the translation files.
 *
 * @author Alexey Bob <alexey.bob@gmail.com>
 *
 * @final
 * @example php artisan translation:update en --dump-messages --force
 */
class TranslationUpdateCommand extends Command
{
    
    private $messages = [];
    
    private $translations = [];
    
    private $newTranslations = [];
    
    private $existingTranslations = [];
    
    private $nonexistentTranslations = [];
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:update
        
                                {locale : The locale}
                                
                                {path=views : Directory where to load the messages, defaults to views folder}
                                
                                {--force : Should the update be done}
                                
                                {--dump-messages : Should the messages be dumped in the console}
                                
                                {--output-format=json : Override the default output format. Default "json"}
                                
                                {--no-backup : Should backup be disabled}
                                
                                {--clean : Should clean not found messages}
                                
                                {--prefix=__,@lang,trans_choice,@choice : Override the default prefix. Default "__,@lang,trans_choice,@choice"}
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract translations from source code.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // check presence of force or dump-message
        if (true !== $this->option('force') && true !== $this->option('dump-messages')) {
            $this->error('You must choose one of --force or --dump-messages');

            return 1;
        }
        
        // check format
        $supportedFormats = array('json');
        if (!in_array($this->option('output-format'), $supportedFormats)) {
            $this->error('Wrong output format', 'Supported formats are: ' . implode(', ', $supportedFormats).'.');
            $this->error('Supported formats are: ' . implode(', ', $supportedFormats).'.');

            return 1;
        }
        
        // Define Root Paths
        $root_path = resource_path($this->argument('path'));
        if (!is_dir($root_path)) {
            
            $this->error(sprintf('"%s" is neither a file nor a directory of resources.', $this->argument('path')));
            
            return 1;
        }
        
        $this->alert('Translation Messages Extractor and Dumper');
        $this->comment(sprintf('Generating "<info>%s.%s</info>" translation file', $this->argument('locale'), $this->option('output-format')));
        $this->line('');
        
        // load any messages from templates
        $this->comment('Parsing templates...');
        $this->line('');
        $finder = new Finder(); // https://symfony.com/doc/current/components/finder.html
        $finder->files()->name('*.blade.php')->in($root_path);
        foreach ($finder as $file) {
            // $this->question('File: ' . $this->argument('path') . '/' . $file->getRelativePathname());
            $contents = $file->getContents();
            $prefixs = explode(',', $this->option('prefix'));
            foreach ($prefixs as $prefix) {
                $this->loadAnyMessagesFromTemplates($contents, $prefix);
            }
        }
        
        // load any existing messages from the translation files
        $this->comment('Loading translation files...');
        $this->line('');
        $filePath = resource_path('lang/' . $this->argument('locale') . '.' . $this->option('output-format'));
        if (\File::exists($filePath)) {
            switch ($this->option('output-format')) {
                case 'json': 
                    $this->translations = json_decode(\File::get($filePath), true);
                    if (null === $this->translations) {
                        $this->translations = [];
                    }
                    break;
            }
        }
        
        // Exit if no messages found.
        if (!count($this->messages)) {
            $this->info('No translation messages were found.');

            return;
        }
        
        $resultMessage = 'Translation files were successfully updated';
        
        // show compiled list of messages
        if (true === $this->option('dump-messages')) {
            $this->comment(sprintf('Messages extracted (%d message%s)', count($this->messages), count($this->messages) > 1 ? 's' : ''));
            $this->table(array('Messages', 'Count'), $this->messages);
            $this->line('');
            
            $resultMessage = sprintf('%d message%s successfully extracted', count($this->messages), count($this->messages) > 1 ? 's were' : ' was');
        }
        
        // save the files
        if (true === $this->option('force')) {
            $this->comment('Writing files...');

            // backup
            if (false === $this->option('no-backup') && count($this->translations) > 0) {
                if (false === \File::isDirectory(resource_path('lang/backup/'))) {
                    \File::makeDirectory(resource_path('lang/backup/'));
                }
                \File::copy($filePath, resource_path('lang/backup/' . $this->argument('locale') . '.' . date('U') . '.' . $this->option('output-format')));
            }
            
            $translations = $this->getProcessedTranslations($this->option('clean') ? true : false);
            
            \File::put($filePath, json_encode($translations, JSON_PRETTY_PRINT));
            
            if (true === $this->option('dump-messages')) {
                $resultMessage .= ' and translation files were updated';
            }
        }
        
        $this->info($resultMessage);
        
        return 1;
    }
    
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
}
