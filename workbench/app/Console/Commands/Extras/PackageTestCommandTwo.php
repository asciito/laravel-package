<?php

namespace Workbench\App\Console\Commands\Extras;

use Illuminate\Console\Command;

class PackageTestCommandTwo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:two';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package Command Test Two';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Package command two');

        return self::SUCCESS;
    }
}
