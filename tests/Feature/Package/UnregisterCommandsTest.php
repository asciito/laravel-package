<?php

use Asciito\LaravelPackage\Package\Package;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Workbench\App\Console\Commands\Extras\PackageTestCommandTwo;
use Workbench\App\Console\Commands\PackageTestCommandOne;
use function Pest\Laravel\{artisan};

trait UnregisterPackageCommand
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->withCommands(PackageTestCommandTwo::class)
            ->unregisterCommand(PackageTestCommandOne::class);
    }
}

uses(UnregisterPackageCommand::class);

it('register commands', function () {
    artisan('list')
        ->expectsOutputToContain('package:two')
        ->doesntExpectOutputToContain('package:one')
        ->assertSuccessful();
});

it('run commands', function () {
    artisan('package:two')
        ->expectsOutput('Package command two')
        ->assertSuccessful();

    artisan('package:one')->run();
})->throws(CommandNotFoundException::class);
