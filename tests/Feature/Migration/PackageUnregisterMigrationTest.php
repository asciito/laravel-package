<?php

use Asciito\LaravelPackage\Package\Package;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;

trait packageUnRegistermigrationTest
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('unregister-package')
            ->withMigration([
                $package->getMigrationPath('extra/create_package_test_three_table.php'),
                $package->getMigrationPath('extra/create_package_test_four_table.php'),
            ])
            ->excludeMigration($package->getMigrationPath('extra/create_package_test_four_table.php'))
            ->preventDefaultMigration();
    }
}

uses(PackageUnRegistermigrationTest::class);

test('package has register config files manually', function () {
    expect($this->package)
        ->getPublishableMigration()
            ->toHaveCount(1)
        ->getRegisteredMigration()
            ->toHaveCount(1);
});

test('package has no default migration files register', function () {
    expect(fn () => DB::table('package_test_one')->get())
        ->toThrow(QueryException::class)
    ->and(fn () => DB::table('package_test_two')->get())
        ->toThrow(QueryException::class);
});

it('publish just one migration file', function () {
    expect(database_path('migrations/create_package_test_three_table.php'))
        ->not->toBeFile();

    artisan('vendor:publish', ['--tag' => 'unregister-package-migrations'])
        ->assertSuccessful();

    expect(database_path('migrations/create_package_test_three_table.php'))
        ->toBeFile()
    ->and(database_path('migrations/create_package_test_four_table.php'))
        ->not->toBeFile();

    artisan('migrate')->assertSuccessful();

    assertDatabaseCount('package_test_three', 0);
});
