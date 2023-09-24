<?php

namespace Asciito\LaravelPackage\Package\Contracts;

use Illuminate\Support\Collection;

interface WithMigrations
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
     * @param  bool  $publish if the file(s) should be publishable, by default is true
     */
    public function withMigrations(string|array $migration = [], bool $publish = true): static;

    /**
     * Prevent publishing the default migrations
     *
     * Calling this method ensures that the migrations in the default folder
     * wouldn't be loaded automatically.
     */
    public function preventDefaultMigrations(): static;

    /**
     * An array with the migration file(s) registered
     *
     * @return Collection The migration file(s) registered in the package
     */
    public function getRegisteredMigrations(): Collection;

    /**
     * Get the array with the publishable migration file(s)
     */
    public function getPublishableMigrations(): Collection;

    /**
     * Set the path to the migrations folder
     */
    public function setMigrationPath(string $path): static;

    /**
     * Get the path to the migrations folder
     */
    public function getMigrationPath(string $path = ''): string;

    /**
     * Un-register a previously registered migration
     */
    public function unregisterMigration(string $path): static;

    /**
     * Un-publish a previously published migration
     */
    public function unpublishMigration(string $path): static;
}
