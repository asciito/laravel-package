<?php

namespace Asciito\LaravelPackage\Package\Contracts;

use Illuminate\Support\Collection;

interface WithMigration
{
    /**
     * Check if the package has migration file(s) available
     *
     * @return bool true if the package has migration file(s), false otherwise
     */
    public function hasMigrations(): bool;

    /**
     * Register the migration file(s) for the package
     *
     * This will register the migration file(s) and will set the migration to be publishable,
     * and to set some files as publishable, and others not, just register the migration file one
     * by one, or in chunks.
     *
     * By calling this method this will try to load your migration
     * file(s) from the default migration folder of your package.
     *
     * @param  string|string[]  $migration The file(s) you want to register
     * @param  bool  $publish if the file(s) should be publishable, by default is true
     */
    public function withMigration(string|array $migration = [], bool $publish = true): static;

    /**
     * Exclude a migration from being register
     */
    public function excludeMigration(string|array $path): static;

    /**
     * An array with the migration file(s) registered
     *
     * @return Collection The migration file(s) registered in the package
     */
    public function getRegisteredMigration(): Collection;

    /**
     * Get the array with the publishable migration file(s)
     */
    public function getPublishableMigration(): Collection;

    /**
     * Set the path to the migration folder
     */
    public function setMigrationPath(string $path): static;

    /**
     * Get the path to the migration folder
     */
    public function getMigrationPath(string $path = ''): string;

    /**
     * Get the files from the migration path
     */
    public function getDefaultMigrationFiles(): Collection;

    /**
     * Prevent publishing the default migration folder
     *
     * Calling this method ensures that the migration files in the default folder
     * wouldn't be loaded automatically.
     */
    public function preventDefaultMigration(): static;
}
