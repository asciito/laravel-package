<?php

namespace Asciito\LaravelPackage\Tests\Feature\Command;

use Asciito\LaravelPackage\Package\Package;

use Symfony\Component\Console\Exception\CommandNotFoundException;
use function Pest\Laravel\artisan;

trait PackageUnregisterCommandTest
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('unregister-package')
            ->withCommand(\Workbench\App\Console\Commands\PackageCommandOne::class)
            ->withCommand([
                \Workbench\App\Console\Commands\Extra\PackageCommandThree::class,
                \Workbench\App\Console\Commands\Extra\PackageCommandFour::class,
            ])
            ->excludeCommand(\Workbench\App\Console\Commands\Extra\PackageCommandThree::class)
            ->preventDefaultCommand();
    }
}

uses(PackageUnregisterCommandTest::class);

test('package has registered files from folder', function () {
    expect($this->package)
        ->getRegisteredCommand()
        ->toHaveCount(2);

    artisan('list')
        ->expectsOutputToContain('package:test-one')
        ->doesntExpectOutputToContain('package:test-two')
        ->doesntExpectOutputToContain('package:test-three')
        ->expectsOutputToContain('package:test-four')
        ->assertSuccessful();
});

it('run registered commands', function () {
    artisan('package:test-one')
        ->expectsOutput('Package test one')
        ->assertSuccessful();

    expect(function () {
        artisan('package:test-two')
            ->assertFailed();
    })->toThrow(CommandNotFoundException::class)
    ->and(function () {
        artisan('package:test-three')
            ->assertFailed();
    })->toThrow(CommandNotFoundException::class);

    artisan('package:test-four')
        ->expectsOutput('Package test four')
        ->assertSuccessful();
});
