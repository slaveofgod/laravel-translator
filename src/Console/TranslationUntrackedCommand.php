<?php

namespace Translator\Console;

use Illuminate\Console\Command;
use Translator\Services\TranslatorService;


/**
 * A command that parses templates to extract translation messages and
 * compares with untracked messages and displays them to the console.
 *
 * @author Alexey Bob <alexey.bob@gmail.com>
 *
 * @final
 * @example php artisan translation:untracked en --dump-messages --force
 */
class TranslationUntrackedCommand extends Command
{
    private $messages = [];
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:untracked
        
                                {locale : The locale}
                                
                                {--force : Should the update be done}
                                
                                {--dump-messages : Should the messages be dumped in the console}
                                
                                {--no-backup : Should backup not be done}
                                
                                {--dev : Development environment}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update translations with untracked messages.';

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
        
        // Define resource path
        if ($this->option('dev')) {
            $resource_path = \Config::get('resource_path');
        } else {
            $resource_path = resource_path();
        }
        
        $this->alert('Translation Messages Extractor and Dumper');
        $this->translator = new TranslatorService(
            $this->argument('locale'),
            $resource_path,
            null,
            null,
            $this->option('dev'),
            null,
            'untracked'
        );

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