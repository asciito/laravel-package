<?php

namespace Workbench\App;

use Asciito\LaravelPackage\Package\Package;

class PackageServiceProvider extends \Asciito\LaravelPackage\Package\PackageServiceProvider
{
    public static \Closure $configureClosure;

    /**
     * @inheritDoc
     */
    protected function configurePackage(Package $package): void
    {
        self::$configureClosure ??= fn (Package $package) => null;

        forward_static_call(static::$configureClosure, $package);
    }
}
