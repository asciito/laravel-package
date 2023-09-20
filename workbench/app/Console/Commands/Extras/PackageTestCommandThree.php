<?php

namespace Workbench\App\Console\Commands\Extras;

use Illuminate\Console\Command;

class PackageTestCommandThree extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:three';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package Command Test Three';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Package command three');

        return self::SUCCESS;
    }
}
