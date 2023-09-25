<?php

namespace Asciito\LaravelPackage\Package\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

trait HasCommands
{
    protected array $commands = [];

    protected bool $preventLoadDefaultCommandsFolder = false;

    protected bool $shouldLoadCommandsFromDefaultFolder = false;

    protected array $excludedCommands = [];

    public function hasCommands(): bool
    {
        return $this->shouldLoadDefaultCommandsFolder() || filled($this->commands);
    }

    private function shouldLoadDefaultCommandsFolder(): bool
    {
        return ! $this->preventLoadDefaultCommandsFolder && $this->shouldLoadCommandsFromDefaultFolder;
    }

    public function preventDefaultCommands(): static
    {
        $this->preventLoadDefaultCommandsFolder = true;

        return $this;
    }

    public function withCommands(string|array ...$command): static
    {
        if (filled($command)) {
            $this->commands = collect($command)
                ->flatten()
                ->merge($this->commands)
                ->all();
        }

        $this->shouldLoadCommandsFromDefaultFolder = true;

        return $this;
    }

    public function getRegisteredCommands(): Collection
    {
        $commands = [];

        if ($this->shouldLoadDefaultCommandsFolder()) {
            $commands = $this->loadCommandsFromDefaultFolder();
        }

        return collect($this->commands)
            ->merge($commands)
            ->filter(fn (string $command) => ! in_array($command, $this->excludedCommands));
    }

    public function loadCommandsFromDefaultFolder()
    {
        return collect(File::files($this->getBasePath('Console/Commands')))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
            ->map(fn (SplFileInfo $file) => str($file)
                ->after('Console/')
                ->replace('/', '\\')
                ->remove('.php')
                ->prepend($this->getNamespace().'\\Console\\')
                ->toString()
            )
            ->all();
    }

    /**
     * Un-register a previously registered command
     */
    public function unregisterCommand(string $fqcn): static
    {
        $this->excludedCommands[] = $fqcn;

        return $this;
    }
}
