<?php

namespace Mtolhuys\LaravelSchematics\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schematics:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install\'s laravel-schematics package';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        try {
            Artisan::call('route:cache');
        } catch (\LogicException $e) {
            $this->warn('Routes couldn\'t be cached because of Closure.');
            $this->error($e->getMessage());
        }

        Artisan::call('vendor:publish', [
            '--provider' => 'Mtolhuys\LaravelSchematics\LaravelSchematicsServiceProvider',
            '--force' => true,
        ]);


        $this->comment(Artisan::output());
    }
}
