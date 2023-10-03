<?php

namespace Asciito\LaravelPackage\Package\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasCommand
{
    protected bool $preventLoadDefaultCommandFolder = false;

    protected bool $shouldIncludeCommandFromFolder = false;

    public function hasCommand(): bool
    {
        return $this->shouldLoadDefaultCommandFolder()
            || $this->getRegister('command')->isNotEmpty();
    }

    public function withCommand(string|array $command = []): static
    {
        $this->ensureRegistersInitialize('command');

        if (filled($command)) {
            $this->register(
                'command',
                $command,
            );
        }

        $this->shouldIncludeCommandFromFolder = true;

        return $this;
    }

    public function excludeCommand(string|array $path): static
    {
        $this->exclude('command', $path);

        return $this;
    }

    public function getRegisteredCommand(): Collection
    {
        $files = $this->getDefaultCommandFiles();

        return $files
            ->merge($this->getRegister('command'))
            ->filter(fn (string $path) => ! in_array($path, $this->getExclude('command')->all()));
    }

    public function getDefaultCommandFiles(): Collection
    {
        if (! $this->shouldLoadDefaultCommandFolder()) {
            return collect();
        }

        return $this->getFilesFrom($this->getBasePath('console/commands'))
            ->map(fn (string $path): string => Str::of($path)
                ->basename('.php')
                ->prepend(
                    '\\',
                    $this->getNamespace(),
                    '\\Console\\Commands\\',
                ),
            );

    }

    public function preventDefaultCommand(): static
    {
        $this->preventLoadDefaultCommandFolder = true;

        return $this;
    }

    private function shouldLoadDefaultCommandFolder(): bool
    {
        return ! $this->preventLoadDefaultCommandFolder
            && $this->shouldIncludeCommandFromFolder;
    }
}
