<?php

use Asciito\LaravelPackage\Package\Package;
use Workbench\App\Console\Commands\Extras\PackageTestCommandThree;
use Workbench\App\Console\Commands\Extras\PackageTestCommandTwo;

use function Pest\Laravel\{artisan};

trait PackageWithCommands
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->withCommands(PackageTestCommandTwo::class)
            ->withCommands([PackageTestCommandThree::class]);
    }
}

uses(PackageWithCommands::class);

it('register commands', function () {
    artisan('list')
        ->expectsOutputToContain('package:one')
        ->expectsOutputToContain('package:two')
        ->expectsOutputToContain('package:three')
        ->expectsOutputToContain('package:four')
        ->assertSuccessful();
});

it('run commands', function () {
    artisan('package:one')
        ->expectsOutput('Package command one')
        ->assertSuccessful();

    artisan('package:two')
        ->expectsOutput('Package command two')
        ->assertSuccessful();

    artisan('package:three')
        ->expectsOutput('Package command three')
        ->assertSuccessful();

    artisan('package:four')
        ->expectsOutput('Package command four')
        ->assertSuccessful();
});
