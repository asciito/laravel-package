<?php

namespace Workbench\Tests;

use Asciito\LaravelPackage\Package\Package;
use Asciito\LaravelPackage\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Workbench\App\Nested\NestedServiceProvider;
use Workbench\App\ServiceProvider;

use function Spatie\PestPluginTestTime\testTime;

abstract class ServiceProviderTest extends TestCase
{
    protected static array $configFilesNames = [
        'one.php',
        'two.php',
        'three.php',
        'nested-one.php',
        'nested-two.php',
    ];

    protected function setUp(): void
    {
        ServiceProvider::$configurePackageUsing = fn (Package $package) => $this->configurePackage($package);

        NestedServiceProvider::$configureNestedUsing = fn (Package $package) => $this->configureNestedService($package);

        testTime()->freeze('2023-01-01 00:00:00');

        parent::setUp();

        $this->deletePublishable();
    }

    protected function configurePackage(Package $package): void
    {
        //
    }

    protected function configureNestedService(Package $package): void
    {
        //
    }

    protected function getPackageProviders($app): array
    {
        return [
            NestedServiceProvider::class,
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

        collect(File::files(database_path('migrations')))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
            ->each(fn (SplFileInfo $file) => File::delete($file));
    }
}
