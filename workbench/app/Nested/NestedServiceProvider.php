<?php

namespace Workbench\App\Nested;

use Asciito\LaravelPackage\Package\Package;
use Asciito\LaravelPackage\Package\PackageServiceProvider;

class NestedServiceProvider extends PackageServiceProvider
{
    public static ?\Closure $configureNestedUsing = null;

    protected function configurePackage(Package $package): void
    {
        $configClosure = self::$configureNestedUsing ?? fn (Package $package) => null;

        $configClosure($package);
    }

    protected function getNamespace(): string
    {
        return __NAMESPACE__;
    }
}
