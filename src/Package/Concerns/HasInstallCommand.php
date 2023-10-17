<?php

namespace Asciito\LaravelPackage\Package\Concerns;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;

trait HasInstallCommand
{
    protected string $commandSignature;

    protected ?Closure $afterCallback;

    protected array $components = [
        'config' => 'Install Config Component',
        'migrations' => 'Install Migrations Component',
    ];

    protected bool $commandShouldAdded = false;

    public function hasInstallCommand(): bool
    {
        return $this->commandShouldAdded;
    }

    public function withInstallCommand(string $signature = '', Closure $after = null): static
    {
        $this->commandSignature = $signature;

        $this->afterCallback = $after;

        $this->commandShouldAdded = true;

        return $this;
    }

    public function command(Command $command): int
    {
        intro('Installing Package Components');

        $all = $command->option('all');

        $some = false;

        foreach ($this->components as $name => $description) {
            $name = str($name);

            if ($all || $command->option($name)) {
                spin(function () use ($name) {
                    Artisan::call(
                        'vendor:publish',
                        ['--tag' => $this->prefixWithPackageName($name)]
                    );
                }, "Publishing Component [{$name->title()}] files...");

                $some = true;
            }
        }

        call_user_func($this->afterCallback, $command);

        $message = 'Package Component(s) installed';

        if (! $some) {
            $message = 'None package components were installed';
        }

        outro($message);

        return $command::SUCCESS;
    }

    public function getInstallCommandSignature(): string
    {
        if (filled($this->commandSignature)) {
            return $this->commandSignature;
        }

        return $this->prefixWithPackageName('install', ':');
    }

    public function preConfigureInstallCommand(Command $command): void
    {
        foreach ($this->components as $component => $description) {
            $command->addOption($component, description: $description);
        }

        $command->addOption('all', description: 'Install all the package components');

        $command::macro('sponsor', function (string $message, string $url) use ($command) {
            $ans = $command->confirm($message);

            if (! $ans) {
                return;
            }

            $url = urlencode($url);

            if (PHP_OS_FAMILY == 'Darwin') {
                exec("open $url");
            }

            if (PHP_OS_FAMILY == 'Windows') {
                exec("start $url");
            }

            if (PHP_OS_FAMILY == 'Linux') {
                exec("xdg-open $url");
            }

            $command->info('Thanks!');
        });
    }
}
