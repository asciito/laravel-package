<?php

namespace Workbench\App;

use Asciito\LaravelPackage\Package\Package;
use Asciito\LaravelPackage\Package\PackageServiceProvider;

class ServiceProvider extends PackageServiceProvider
{
    public static ?\Closure $configurePackageUsing = null;

    protected function configurePackage(Package $package): void
    {
        $configClosure = self::$configurePackageUsing ?? fn (Package $package) => null;

        $configClosure($package);
    }

    protected function getNamespace(): string
    {
        return __NAMESPACE__;
    }
}
