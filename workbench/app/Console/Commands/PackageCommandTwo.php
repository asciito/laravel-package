<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;

class PackageCommandTwo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:test-two';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package Test Two';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Package test two');

        return self::SUCCESS;
    }
}
