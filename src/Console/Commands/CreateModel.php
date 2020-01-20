<?php

namespace Mtolhuys\LaravelSchematics\Console\Commands;

use Illuminate\Console\Command;

class CreateModel extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
        create:model
            { name : Specify model name. }
            { columns* : Specify columns f.e. "name varchar(255)"}
    ';

    private $scanner;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model with given name and columns';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
       $name = $this->argument('name');
       $columns = $this->argument('columns');

       dd($name, $columns);
    }
}
