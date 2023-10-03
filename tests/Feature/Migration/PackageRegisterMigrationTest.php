<?php

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;

trait PackageRegisterMigrationTest
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('package')
            ->withMigration($package->getMigrationPath('extra/create_package_test_three_table.php'))
            ->withMigration([$package->getMigrationPath('extra/create_package_test_four_table.php')]);
    }
}

uses(PackageRegisterMigrationTest::class);

test('package has registered files from folder', function () {
    expect($this->package)
        ->getPublishableMigration()
        ->toHaveCount(4)
        ->getRegisteredMigration()
        ->toHaveCount(4);
});

test('package register migration files without publishing it', function () {
    expect(database_path('migrations/create_package_test_one_table.php'))
        ->not->toBeFile()
        ->and(database_path('migrations/create_package_test_two_table.php'))
        ->not->toBeFile()
        ->and(database_path('migrations/create_package_test_three_table.php'))
        ->not->toBeFile()
        ->and(database_path('migrations/create_package_test_four_table.php'))
        ->not->toBeFile();

    artisan('migrate')
        ->assertSuccessful();

    assertDatabaseCount('package_test_one', 0);
    assertDatabaseCount('package_test_two', 0);
    assertDatabaseCount('package_test_three', 0);
    assertDatabaseCount('package_test_four', 0);
});

it('publish package migration files', function () {
    expect(database_path('migrations/create_package_test_one_table.php'))
        ->not->toBeFile()
        ->and(database_path('migrations/create_package_test_two_table.php'))
        ->not->toBeFile()
        ->and(database_path('migrations/create_package_test_three_table.php'))
        ->not->toBeFile()
        ->and(database_path('migrations/create_package_test_four_table.php'))
        ->not->toBeFile();

    artisan('vendor:publish', ['--tag' => 'package-migrations']);

    expect(database_path('migrations/create_package_test_one_table.php'))
        ->toBeFile()
        ->and(database_path('migrations/create_package_test_two_table.php'))
        ->toBeFile()
        ->and(database_path('migrations/create_package_test_three_table.php'))
        ->toBeFile()
        ->and(database_path('migrations/create_package_test_four_table.php'))
        ->toBeFile();

    artisan('migrate');

    assertDatabaseCount('package_test_one', 0);
    assertDatabaseCount('package_test_two', 0);
    assertDatabaseCount('package_test_three', 0);
    assertDatabaseCount('package_test_four', 0);
});
