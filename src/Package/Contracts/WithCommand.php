<?php

namespace Asciito\LaravelPackage\Package\Contracts;

use Illuminate\Support\Collection;

interface WithCommand
{
    /**
     * Check if the package has command file(s) available
     *
     * @return bool true if the package has command file(s), false otherwise
     */
    public function hasCommand(): bool;

    /**
     * Register the command file(s) for the package
     *
     * This will register the command file(s) and will set the command to be publishable,
     * and to set some files as publishable, and others not, just register the command file one
     * by one, or in chunks.
     *
     * By calling this method this will try to load your command
     * file(s) from the default command folder of your package.
     *
     * @param  string|string[]  $command The file(s) you want to register
     */
    public function withCommand(string|array $command = []): static;

    /**
     * Exclude a command from being register
     */
    public function excludeCommand(string|array $path): static;

    /**
     * An array with the command file(s) registered
     *
     * @return Collection The command file(s) registered in the package
     */
    public function getRegisteredCommand(): Collection;

    /**
     * Get the files from the command path
     */
    public function getDefaultCommandFiles(): Collection;

    /**
     * Prevent publishing the default command folder
     *
     * Calling this method ensures that the command files in the default folder
     * wouldn't be loaded automatically.
     */
    public function preventDefaultCommand(): static;
}
