<?php

namespace Translator\Console;

use Illuminate\Console\Command;
use Translator\Services\TranslatorService;


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
                                
                                {--no-backup : Should backup not be done}
                                
                                {--clean : Should clean not found messages}
                                
                                {--prefix=__,@lang,trans_choice,@choice,__ab,@lang_ab,trans_choice_ab,@choice_ab : Override the default prefix. Default "__,@lang,trans_choice,@choice,__ab,@lang_ab,trans_choice_ab,@choice_ab"}
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update translations from source code.';

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
        // validate locale
        $validator = \Validator::make(['locale' => $this->argument('locale')], ['locale' => 'required|locale']);
        if ($validator->fails()) {
            $this->error('Locale Errors:');
            foreach ($validator->errors()->getMessages() as $key => $errors) {
                foreach ($errors as $error) {
                    $this->error('  • ' . $key . ': ' .  $error);
                }
            }
            
            return 1;
        }
        
        // check presence of force or dump-message
        if (true !== $this->option('force') && true !== $this->option('dump-messages')) {
            $this->error('You must choose one of --force or --dump-messages');

            return 1;
        }
        
        // Define Root Paths
        $root_path = resource_path($this->argument('path'));
        if (!is_dir($root_path)) {
            
            $this->error(sprintf('"%s" is neither a file nor a directory of resources.', $this->argument('path')));
            
            return 1;
        }
        
        $this->alert('Translation Messages Extractor and Dumper');
        $this->translator = new TranslatorService($this->argument('locale'), $root_path, $this->option('prefix'));

        $this->comment('Parsing templates...');
        $this->line('');
        $this->comment('Loading translation files...');
        $this->line('');
        
        $this->messages = $this->translator->getMessages();

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
            
            // add new messages
            $this->translator->addNewMessages();
            
            if (true === $this->option('dump-messages')) {
                $resources = $this->translator->getResources();
                foreach ($resources as $resource) {
                    if ($resource->hasNewMessages()) {
                        $this->line('');
                        $this->comment(sprintf('Generating/Updating "<info>%s</info>" translation file', $resource->getRelativePathname()));
                        $this->table(array('Messages'), $resource->getNewMessages());
                    }
                }
            }
            
            // clean not found messages
            if (true === $this->option('clean')) {
                if ($this->confirm('You specify to clean not found messages. Do you wish to clean not found messages?')) {
                    $this->translator->clean();
                }
            }
            
            // save the resources
            $this->translator->save($this->option('no-backup'));
            
            if (true === $this->option('dump-messages')) {
                $resultMessage .= ' and translation files were updated';
            }
        }
        
        $this->info($resultMessage);
        
        return 1;
    }
}