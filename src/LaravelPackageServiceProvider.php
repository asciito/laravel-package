<?php

namespace Asciito\LaravelPackage;

use Asciito\LaravelPackage\Package\Package;
use Asciito\LaravelPackage\Package\PackageServiceProvider;

class LaravelPackageServiceProvider extends PackageServiceProvider
{
    protected function configurePackage(Package $package): void
    {
        $package->setName('laravel-package');
    }

    protected function getNamespace(): string
    {
        return __NAMESPACE__;
    }
}
