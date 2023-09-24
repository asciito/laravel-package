<?php

use Asciito\LaravelPackage\Package\Package;

use Illuminate\Support\Facades\File;
use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertFileExists;

trait PackageWithMigrations
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('test-package-migration')
            ->withMigrations();
    }
}

uses(PackageWithMigrations::class);

it('register migrations', function () {
    artisan('migrate')->run();

    assertdatabasecount('package_test_one', 0);
    assertdatabasecount('package_test_two', 0);
    assertdatabasecount('package_test_three', 0);
    assertdatabasecount('package_test_four', 0);
});

it('published migrations', function () {
    artisan('vendor:publish', ['--tag' => 'test-package-migration-migrations'])
        ->assertSuccessful();

    assertFileExists(database_path('migrations/create_package_test_one_table.php'));
    assertFileExists(database_path('migrations/create_package_test_two_table.php'));
    assertFileExists(database_path('migrations/create_package_test_three_table.php'));
    assertFileExists(database_path('migrations/create_package_test_four_table.php'));

    artisan('migrate')->assertSuccessful();

    assertdatabasecount('package_test_one', 0);
    assertdatabasecount('package_test_two', 0);
    assertdatabasecount('package_test_three', 0);
    assertdatabasecount('package_test_four', 0);
});
