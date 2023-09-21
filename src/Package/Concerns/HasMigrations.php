<?php

namespace Asciito\LaravelPackage\Package\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

trait HasMigrations
{
    protected string $migrationsPath;

    protected array $migrations = [];

    protected bool $preventLoadDefault = false;

    protected bool $shouldIncludeMigrationsFromFolder = false;

    public function hasMigrations(): bool
    {
        return $this->shouldLoadDefaultMigrationsFolder() || filled($this->migrations);
    }

    public function withMigrations(string|array $migration = [], bool $publish = true): static
    {
        if (filled($migration)) {
            $this->migrations = collect($migration)
                ->mapWithKeys(fn (string $value) => [$value => $publish])
                ->merge($this->migrations)
                ->all();
        }

        $this->shouldIncludeMigrationsFromFolder = true;

        return $this;
    }

    public function preventDefaultMigrations(): static
    {
        $this->preventLoadDefault = true;

        return $this;
    }

    public function getRegisteredMigrations(): Collection
    {
        $migrations = [];

        if ($this->shouldLoadDefaultMigrationsFolder()) {
            $migrations = $this->loadDefaultFolder();
        }

        return collect($this->migrations)->keys();
    }

    public function getPublishableMigrations(): Collection
    {
        $migrations = [];

        if ($this->shouldLoadDefaultMigrationsFolder()) {
            $migrations = $this->loadDefaultFolder();
        }

        return collect($this->migrations)
            ->merge($migrations)
            ->filter()
            ->keys();
    }

    public function setMigrationPath(string $path): static
    {
        $this->migrationsPath = rtrim($path, DIRECTORY_SEPARATOR);

        return $this;
    }

    public function getMigrationPath(string $path = ''): string
    {
        return join_paths($this->migrationsPath, $path);
    }

    private function shouldLoadDefaultMigrationsFolder(): bool
    {
        return ! $this->preventLoadDefault && $this->shouldIncludeMigrationsFromFolder;
    }

    private function loadDefaultFolder(): array
    {
        $migrations = collect(File::files($this->getMigrationPath()))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
            ->mapWithKeys(fn (SplFileInfo $file) => [(string) $file => true])
            ->all();

        return $migrations;
    }
}
