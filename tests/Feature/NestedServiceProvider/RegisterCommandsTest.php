<?php

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\artisan;

trait NestedServiceProviderWithCommands
{
    protected function configureNestedService(Package $package): void
    {
        $package
            ->setName('nested-service')
            ->withCommands();
    }
}

uses(NestedServiceProviderWithCommands::class);

it('register commands', function () {
    artisan('list')
        ->expectsOutputToContain('project:one')
        ->expectsOutputToContain('project:two')
        ->assertSuccessful();
});

it('run commands', function () {
    artisan('project:one')
        ->expectsOutput('Project command one')
        ->assertSuccessful();

    artisan('project:two')
        ->expectsOutput('Project command two')
        ->assertSuccessful();
});
