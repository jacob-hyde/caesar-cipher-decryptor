<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\Decrypt;
class DecryptCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cipher:decrypt {--file=} {--keyFile=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Decrypt a cipher of text in a file with a key file';

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
        ini_set('memory_limit', '12096M');
        $file = $this->option('file');
        $key = $this->option('keyFile');
        $decrypt = new Decrypt(file_get_contents(storage_path('app/'.$file)), file_get_contents(storage_path('app/'.$key)));
        $bar = $this->output->createProgressBar(count($decrypt->cipherWordList));
        $bar->start();
        $decrypt->bar = $bar;
        $decrypted = $decrypt->decrypt();
        $bar->finish();
        echo $decrypted;
    }
}
