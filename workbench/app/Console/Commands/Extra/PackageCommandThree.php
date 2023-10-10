<?php

namespace Workbench\App\Console\Commands\Extra;

use Illuminate\Console\Command;

class PackageCommandThree extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:test-three';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package Test Three';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Package test three');

        return self::SUCCESS;
    }
}
