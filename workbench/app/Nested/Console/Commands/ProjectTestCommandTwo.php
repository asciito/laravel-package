<?php

namespace Workbench\App\Nested\Console\Commands;

use Illuminate\Console\Command;

class ProjectTestCommandTwo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:two';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'project Command Test Two';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Project command two');

        return self::SUCCESS;
    }
}
