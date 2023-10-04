<?php

namespace Asciito\LaravelPackage\Package\Contracts;

use Closure;
use Illuminate\Console\Command;

interface WithInstallCommand
{
    /**
     * Check if the installation command is available
     */
    public function hasInstallCommand(): bool;

    /**
     * Register and configure the installation command
     */
    public function withInstallCommand(string $signature = '', Closure $after = null): static;

    /**
     * The installation command itself
     *
     * @param  Command  $command The instance of the command to be run as the installation command
     */
    public function command(Command $command): int;

    /**
     * Return the command signature
     */
    public function getInstallCommandSignature(): string;

    /**
     * Pre-configure the command with a fresh instance of the command
     */
    public function preConfigureInstallCommand(Command $command): void;
}
