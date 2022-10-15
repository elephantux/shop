<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{

    protected $signature = 'shop:install';
    protected $description = 'Shop installation';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('storage:link');
        $this->call('migrate');

        return self::SUCCESS;
    }
}
