<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\Enigma\Enigma;
class EnigmaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cipher:enigma {--file=} {--keyFile=}';

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
        $decrypt = new Enigma(file_get_contents(storage_path('app/'.$file)), file_get_contents(storage_path('app/'.$key)));
        $decrypt->output = $this->output;
        $decrypted = $decrypt->decrypt();
        echo $decrypted;
    }
}
