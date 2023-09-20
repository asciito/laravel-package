<?php

namespace Workbench\Tests;

use Asciito\LaravelPackage\Package\Package;
use Asciito\LaravelPackage\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Workbench\App\ServiceProvider;

use function Spatie\PestPluginTestTime\testTime;

abstract class ServiceProviderTest extends TestCase
{
    protected static array $configFilesNames = [
        'one.php',
        'two.php',
        'three.php',
    ];

    protected function setUp(): void
    {
        ServiceProvider::$configurePackageUsing = fn (Package $package) => $this->configurePackage($package);

        testTime()->freeze('2023-01-01 00:00:00');

        parent::setUp();

        $this->deletePublishable();
    }

    abstract protected function configurePackage(Package $package);

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function tearDown(): void
    {
        $this->deletePublishable();

        parent::tearDown();
    }

    protected function deletePublishable(): void
    {
        File::delete(
            collect(static::$configFilesNames)
                ->map(fn (string $file) => config_path($file))
                ->all()
        );

        File::cleanDirectory(database_path('migrations'));
    }
}
