<?php

namespace Asciito\LaravelPackage\Package\Contracts;

use Illuminate\Support\Collection;

interface WithCommands
{
    /**
     * Check if the package has commands to register
     *
     * @return bool true if the package has commands, false otherwise
     */
    public function hasCommands(): bool;

    /**
     * Register the config file(s) for the package
     *
     * By calling this method this will try to load your
     * file(s) from the default command folder of your package.
     *
     * @param  string|string[]  $command The command(s) you want to register (the FQCN)
     */
    public function withCommands(string|array ...$command): static;

    /**
     * An array with the commands registered
     *
     * @return Collection The commands registered in the package
     */
    public function getRegisteredCommands(): Collection;
}
