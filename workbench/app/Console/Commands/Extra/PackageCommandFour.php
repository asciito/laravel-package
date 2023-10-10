<?php

namespace Workbench\App\Console\Commands\Extra;

use Illuminate\Console\Command;

class PackageCommandFour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:test-four';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package Test Four';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Package test four');

        return self::SUCCESS;
    }
}
