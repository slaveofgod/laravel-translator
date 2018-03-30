<?php

namespace Translator\Console;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;


//php artisan translation:update --dump-messages --force fr
//php artisan translation:update --dump-messages --force en AppBundle

class TranslationExtractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:extract {--dump-messages} {--force} {locale}';

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
     
//        dd( $this->options() );
        dd( $this->arguments() );
        
        $aa = $this->argument('locale');
        $bb = $this->option('--dump-messages');
        $cc = $this->option('--force');
        
        dd( $aa, $bb, $cc );
        
        $finder = new Finder();
        $finder->files()->in(__DIR__);

        foreach ($finder as $file) {
            // dumps the absolute path
            var_dump($file->getRealPath());

            // dumps the relative path to the file, omitting the filename
            var_dump($file->getRelativePath());

            // dumps the relative path to the file
            var_dump($file->getRelativePathname());
        }
    }
}
