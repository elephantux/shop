<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class RefreshCommand extends Command
{
    protected $signature = 'shop:refresh';
    protected $description = 'Refresh shop data';

    public function handle(): int
    {
        if (app()->isProduction()) {
            return self::FAILURE;
        }

        File::cleanDirectory(Storage::path('public/images/products'));
        File::cleanDirectory(Storage::path('public/images/brands'));

        $this->call('migrate:fresh', [
            '--seed' => true
        ]);

        return self::SUCCESS;
    }
}
