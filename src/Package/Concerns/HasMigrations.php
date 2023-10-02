<?php

namespace Asciito\LaravelPackage\Package\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasMigrations
{
    protected string $migrationPath;

    protected bool $preventLoadDefaultMigrationFolder = false;

    protected bool $shouldIncludeMigrationFromFolder = false;

    public function hasMigrations(): bool
    {
        return $this->shouldLoadDefaultMigrationFolder()
            || $this->getRegister('migrations')->isNotEmpty();
    }

    public function withMigration(string|array $migration = [], bool $publish = true): static
    {
        $this->ensureRegistersInitialize('migrations');

        if (filled($migration)) {
            $this->register(
                'migrations',
                Arr::mapWithKeys(Arr::wrap($migration), fn (string $path): array => [$path => $publish])
            );
        }

        $this->shouldIncludeMigrationFromFolder = true;

        return $this;
    }

    public function excludeMigration(string|array $path): static
    {
        $this->exclude('migrations', Arr::wrap($path));

        return $this;
    }

    public function getRegisteredMigration(): Collection
    {
        $files = $this->getDefaultMigrationFiles();

        return $files
            ->merge($this->getRegister('migrations'))
            ->except($this->getExclude('migrations'))
            ->keys();
    }


    public function getPublishableMigration(): Collection
    {
        $files = $this->getDefaultMigrationFiles();

        return $files
            ->merge($this->getRegister('migrations'))
            ->filter()
            ->except($this->getExclude('migrations'))
            ->mapWithKeys(fn (bool $_, string $path) => [
                $path => database_path('migrations/'.basename($path))
            ]);
    }

    public function getMigrationPath(string $path = ''): string
    {
        return join_paths($this->migrationPath, $path);
    }

    public function setMigrationPath(string $path): static
    {
        $this->migrationPath = rtrim($path, DIRECTORY_SEPARATOR);

        return $this;
    }

    public function getDefaultMigrationFiles(): Collection
    {
        if (! $this->shouldLoadDefaultMigrationFolder()) {
            return collect();
        }

        return $this->getFilesFrom($this->migrationPath)
            ->mapWithKeys(fn (string $path) => [$path => true]);
    }

    public function preventDefaultMigration(): static
    {
        $this->preventLoadDefaultMigrationFolder = true;

        return $this;
    }

    private function shouldLoadDefaultMigrationFolder(): bool
    {
        return ! $this->preventLoadDefaultMigrationFolder && $this->shouldIncludeMigrationFromFolder;
    }
}
