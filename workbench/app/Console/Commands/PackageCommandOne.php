<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;

class PackageCommandOne extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:test-one';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package Test One';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Package test one');

        return self::SUCCESS;
    }
}
