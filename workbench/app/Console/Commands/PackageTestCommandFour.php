<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;

class PackageTestCommandFour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:four';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package Command Test Four';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Package command four');

        return self::SUCCESS;
    }
}
