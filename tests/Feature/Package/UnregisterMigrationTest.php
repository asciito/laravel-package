<?php

namespace Asciito\LaravelPackage\Tests\Feature\Package;

use Asciito\LaravelPackage\Package\Package;
use Illuminate\Database\QueryException;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertFileDoesNotExist;

trait UnregisterMigrationTest
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('unregister-migration')
            ->withMigrations()
            ->unregisterMigration('create_package_test_one_table.php')
            ->unpublishMigration('create_package_test_two_table.php');
    }
}

uses(UnregisterMigrationTest::class);

test('un-register migrations', function () {
    artisan('migrate')->run();

    // Will throw and error if the database not exist.
    assertDatabaseCount('package_test_one', 0);
})->throws(QueryException::class);

test('un-publish migration', function () {
    artisan('vendor:publish', ['--tag' => 'unregister-migration-migrations'])->run();

    assertFileDoesNotExist(database_path('migrations/create_package_test_one_table.php'));
    assertFileDoesNotExist(database_path('migrations/create_package_test_two_table.php'));

    artisan('migrate')->run();

    assertDatabaseCount('package_test_two', 0);
});
