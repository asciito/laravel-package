<?php

use Asciito\LaravelPackage\Package\Package;
use Laravel\Prompts\Prompt;

use function Pest\Laravel\artisan;

trait PackageInstallCommandTest
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('install-package')
            ->withConfig()
            ->withMigration()
            ->withInstallCommand(
                'package:install',
                function ($command) {
                    $command->info('This is a message');
                }
            );
    }
}

uses(PackageInstallCommandTest::class);

it('install command is registered', function () {
    Prompt::fake();

    artisan('list')
        ->expectsOutputToContain('package:install')
        ->assertSuccessful();

    artisan('package:install')
        ->expectsOutputToContain('Installing Package Components')
        ->expectsOutput('This is a message')
        ->expectsOutputToContain('None package components were installed')
        ->assertSuccessful();
});

it('install config', function () {
    artisan('package:install --config')
        ->expectsOutputToContain('Publishing Component [Config] files...')
        ->doesntExpectOutputToContain('Publishing Component [Migration] files')
        ->doesntExpectOutputToContain('None package components were installed')
        ->expectsOutputToContain('Package Component(s) installed')
        ->assertSuccessful();

    expect(config_path('one.php'))
        ->toBeFile()
        ->and(config_path('two.php'))
        ->toBeFile();
});

it('install migrations', function () {
    artisan('package:install --migrations')
        ->expectsOutputToContain('Publishing Component [Migrations] files...')
        ->doesntExpectOutputToContain('Publishing Component [Config] files')
        ->doesntExpectOutputToContain('None package components were installed')
        ->expectsOutputToContain('Package Component(s) installed')
        ->assertSuccessful();

    expect(database_path('migrations/create_package_test_one_table.php'))
        ->toBeFile()
        ->and(database_path('migrations/create_package_test_two_table.php'))
        ->toBeFile();
});

it('install all the components', function () {
    artisan('package:install --all')
        ->expectsOutputToContain('Publishing Component [Migrations] files...')
        ->expectsOutputToContain('Publishing Component [Config] files')
        ->expectsOutputToContain('Package Component(s) installed')
        ->doesntExpectOutputToContain('None package components were installed')
        ->assertSuccessful();
});
