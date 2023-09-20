<?php

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertFileExists;

trait PackageWithMigrations
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->preventDefaultMigrations()
            ->withMigrations([
                $package->getMigrationPath('create_package_test_one_table.php'),
                $package->getMigrationPath('create_package_test_two_table.php'),
                $package->getMigrationPath('create_package_test_three_table.php'),
                $package->getMigrationPath('create_package_test_four_table.php'),
            ]);
    }
}

uses(PackageWithMigrations::class);

it('publish migrations', function () {
    artisan('vendor:publish', ['--tag' => 'test-package-migrations'])
        ->doesntExpectOutputToContain('No publishable resources for tag [test-package-migration]')
        ->assertSuccessful();

    assertFileExists(database_path('migrations/2023_01_01_000000_create_package_test_one_table.php'));
    assertFileExists(database_path('migrations/2023_01_01_000001_create_package_test_two_table.php'));
    assertFileExists(database_path('migrations/2023_01_01_000002_create_package_test_three_table.php'));
    assertFileExists(database_path('migrations/2023_01_01_000003_create_package_test_four_table.php'));
});

it('run published migrations', function () {
    artisan('vendor:publish', ['--tag' => 'test-package-migrations'])
        ->doesntExpectOutputToContain('No publishable resources for tag [test-package-migration]')
        ->assertSuccessful();

    artisan('migrate')->assertSuccessful();

    assertdatabasecount('package_test_one', 0);
    assertdatabasecount('package_test_two', 0);
    assertdatabasecount('package_test_three', 0);
    assertdatabasecount('package_test_four', 0);
});
