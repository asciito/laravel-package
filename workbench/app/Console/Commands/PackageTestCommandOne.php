<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;

class PackageTestCommandOne extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:one';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package Command Test One';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Package command one');

        return self::SUCCESS;
    }
}
