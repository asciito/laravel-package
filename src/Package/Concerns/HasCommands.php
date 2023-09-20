<?php

namespace Asciito\LaravelPackage\Package\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

trait HasCommands
{
    protected array $commands = [];

    protected bool $shouldLoadCommandsFromDefaultFolder = false;

    public function hasCommands(): bool
    {
        return filled($this->commands) || $this->shouldLoadCommandsFromDefaultFolder;
    }

    public function withCommands(string|array ...$command): static
    {
        if (filled($command)) {
            $this->commands = collect($command)
                ->flatten()
                ->mapWithKeys(fn (string $value) => [$value => true])
                ->merge($this->commands)
                ->all();
        }

        $this->shouldLoadCommandsFromDefaultFolder = true;

        return $this;
    }

    public function getRegisteredCommands(): Collection
    {
        $commands = [];

        if ($this->shouldLoadCommandsFromDefaultFolder) {
            $commands = $this->loadCommandsFromDefaultFolder();
        }

        return collect($this->commands)
            ->merge($commands)
            ->keys();
    }

    public function loadCommandsFromDefaultFolder()
    {
        return collect(File::files($this->getBasePath('Console/Commands')))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
            ->mapWithKeys(function (SplFileInfo $file) {
                $fqcn = str($file)
                    ->after('Console/')
                    ->replace('/', '\\')
                    ->remove('.php')
                    ->prepend($this->getNamespace().'\\Console\\')
                    ->toString();

                return [$fqcn => true];
            })
            ->all();
    }
}
