<?php

namespace Workbench\App\Nested\Console\Commands;

use Illuminate\Console\Command;

class ProjectTestCommandOne extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:one';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project Command Test One';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Project command one');

        return self::SUCCESS;
    }
}
