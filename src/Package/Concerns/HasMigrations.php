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

    protected array $excludedMigrations = [];

    protected array $unpublishedMigrations = [];

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

        $register = collect($this->migrations)
            ->merge($migrations)
            ->filter(function (bool $_, string $migration) {
                return ! in_array($this->getFileName($migration), $this->excludedMigrations);
            })
            ->keys();

        return $register;
    }

    public function getPublishableMigrations(): Collection
    {
        $migrations = [];

        if ($this->shouldLoadDefaultMigrationsFolder()) {
            $migrations = $this->loadDefaultFolder();
        }

        $publish = collect($this->migrations)
            ->merge($migrations)
            ->filter(function (bool $publish, string $migration) {
                return $publish && ! in_array($this->getFileName($migration), [...$this->excludedMigrations, ...$this->unpublishedMigrations]);
            })
            ->keys();

        return $publish;
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

    /**
     * Un-register a previously registered migration
     */
    public function unregisterMigration(string $path): static
    {
        $this->excludedMigrations[] = $this->getFileName($path);

        return $this;
    }

    /**
     * Un-publish a previously published migration
     */
    public function unpublishMigration(string $path): static
    {
        $this->unpublishedMigrations[] = $this->getFileName($path);

        return $this;
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
