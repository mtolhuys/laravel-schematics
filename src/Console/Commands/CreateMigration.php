<?php

namespace Mtolhuys\LaravelSchematics\Console\Commands;

use Illuminate\Console\Command;
use Mtolhuys\LaravelSchematics\Actions\GenerateMigration;

class CreateMigration extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
        create:migration
            { name : Specify table name }
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

        (new GenerateMigration($name, []))->execute();
    }
}
