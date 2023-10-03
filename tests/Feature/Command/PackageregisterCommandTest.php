<?php

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\artisan;

trait PackageRegisterCommandTest
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('package')
            ->withCommand([
                \Workbench\App\Console\Commands\Extra\PackageCommandThree::class,
                \Workbench\App\Console\Commands\Extra\PackageCommandFour::class,
            ]);
    }
}

uses(PackageRegisterCommandTest::class);

test('package has registered files from folder', function () {
    expect($this->package)
        ->getRegisteredCommand()
        ->toHaveCount(4);

    artisan('list')
        ->expectsOutputToContain('package:test-one')
        ->expectsOutputToContain('package:test-two')
        ->expectsOutputToContain('package:test-three')
        ->expectsOutputToContain('package:test-four')
        ->assertSuccessful();
});

it('run registered commands', function () {
    artisan('package:test-one')
        ->expectsOutput('Package test one')
        ->assertSuccessful();

    artisan('package:test-two')
        ->expectsOutput('Package test two')
        ->assertSuccessful();

    artisan('package:test-three')
        ->expectsOutput('Package test three')
        ->assertSuccessful();

    artisan('package:test-four')
        ->expectsOutput('Package test four')
        ->assertSuccessful();
});
