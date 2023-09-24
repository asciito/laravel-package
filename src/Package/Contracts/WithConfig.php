<?php

namespace Asciito\LaravelPackage\Package\Contracts;

use Illuminate\Support\Collection;

interface WithConfig
{
    /**
     * Check if the package has configuration file(s) available
     *
     * @return bool true if the package has config file(s), false otherwise
     */
    public function hasConfig(): bool;

    /**
     * Register the config file(s) for the package
     *
     * This will register the config file(s) and will set the config to be publishable,
     * and to set some files as publishable, and others not, just register the config file one
     * by one, or in chunks.
     *
     * By calling this method this will try to load your config
     * file(s) from the default config folder of your package.
     *
     * @param  string|string[]  $config The file(s) you want to register
     * @param  bool  $publish if the file(s) should be publishable, by default is true
     */
    public function withConfig(string|array $config = [], bool $publish = true): static;

    /**
     * Prevent publishing the default config folder
     *
     * Calling this method ensures that the config files in the default folder
     * wouldn't be loaded automatically.
     */
    public function preventDefaultConfig(): static;

    /**
     * An array with the config file(s) registered
     *
     * @return Collection The config file(s) registered in the package
     */
    public function getRegisteredConfig(): Collection;

    /**
     * Get the array with the publishable config file(s)
     */
    public function getPublishableConfig(): Collection;

    /**
     * Set the path to the configuration folder
     */
    public function setConfigPath(string $path): static;

    /**
     * Get the path to the configuration folder
     */
    public function getConfigPath(string $path = ''): string;

    /**
     * Un-register a previously registered config
     */
    public function unregisterConfig(string $path): static;

    /**
     * Un-publish a previously published config
     */
    public function unpublishConfig(string $path): static;
}
