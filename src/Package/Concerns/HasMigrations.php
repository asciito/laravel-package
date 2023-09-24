<?php

namespace Asciito\LaravelPackage\Package\Concerns;

use Illuminate\Support\Collection;

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
                ->map(fn (string $migration) => absolute($migration))
                ->filter()
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
        $migrations = $this->loadMigrationsDefaultFolder();

        return collect($this->migrations)
            ->merge($migrations)
            ->filter(function (bool $_, string $migration) {
                return ! in_array($migration, $this->excludedMigrations);
            })
            ->keys();
    }

    public function getPublishableMigrations(): Collection
    {
        $migrations = $this->loadMigrationsDefaultFolder();

        return collect($this->migrations)
            ->merge($migrations)
            ->filter(function (bool $publish, string $migration) {
                return $publish && ! in_array($migration, [...$this->excludedMigrations, ...$this->unpublishedMigrations]);
            })
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

    /**
     * Un-register a previously registered migration
     */
    public function unregisterMigration(string $path): static
    {
        $this->excludedMigrations[] = absolute($path, $this->getMigrationPath());

        return $this;
    }

    /**
     * Un-publish a previously published migration
     */
    public function unpublishMigration(string $path): static
    {
        $this->unpublishedMigrations[] = absolute($path, $this->getMigrationPath());

        return $this;
    }

    private function shouldLoadDefaultMigrationsFolder(): bool
    {
        return ! $this->preventLoadDefault && $this->shouldIncludeMigrationsFromFolder;
    }

    private function loadMigrationsDefaultFolder(): array
    {
        if (! $this->shouldLoadDefaultMigrationsFolder()) {
            return [];
        }

        return $this->loadFilesFrom($this->getMigrationPath())->all();
    }
}
