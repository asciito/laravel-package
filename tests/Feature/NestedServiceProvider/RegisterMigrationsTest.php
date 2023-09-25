<?php

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertFileExists;

trait NestedServiceProviderWithMigrations
{
    protected function configureNestedService(Package $package): void
    {
        $package
            ->setName('nested-service')
            ->setMigrationPath($package->getBasePath('../../database/migrations/nested'))
            ->withMigrations();

        expect($package)
            ->getMigrationPath()
            ->toBe($package->getBasePath('../../database/migrations/nested'))
            ->getPublishableMigrations()
            ->not->toBeEmpty()
            ->toHaveCount(2)
            ->each
            ->toStartWith($package->getMigrationPath())
            ->toMatch('/\/\w+.php$/');
    }
}

uses(NestedServiceProviderWithMigrations::class);

it('register migrations', function () {
    artisan('migrate')->run();

    assertdatabasecount('nested_test_one', 0);
    assertdatabasecount('nested_test_one', 0);
});

it('publish migrations', function () {
    artisan('vendor:publish', ['--tag' => 'nested-service-migrations'])->run();

    assertFileExists(database_path('migrations/create_nested_test_one_table.php'));
    assertFileExists(database_path('migrations/create_nested_test_two_table.php'));

    artisan('migrate')->assertSuccessful();

    assertdatabasecount('nested_test_one', 0);
    assertdatabasecount('nested_test_one', 0);
});
