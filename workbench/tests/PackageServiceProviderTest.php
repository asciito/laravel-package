<?php

namespace Workbench\Tests;

use Asciito\LaravelPackage\Package\Package;
use Asciito\LaravelPackage\Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Spatie\TestTime\TestTime;
use Symfony\Component\Finder\SplFileInfo;
use Workbench\App\PackageServiceProvider;

use function Spatie\PestPluginTestTime\testTime;

abstract class PackageServiceProviderTest extends TestCase
{
    protected Package $package;

    protected static array $configFilesNames = [
        'one.php',
        'two.php',
        'three.php',
        'four.php',
    ];

    protected function setUp(): void
    {
        PackageServiceProvider::$configureClosure = function (Package $package) {
            $this->package = $package;

            $this->configurePackage($package);
        };

        testTime()->freeze('2023-01-01 00:00:00');

        parent::setUp();

        $this->deletePublishable();
    }

    abstract protected function configurePackage(Package $package): void;

    protected function getPackageProviders($app): array
    {
        return [
            PackageServiceProvider::class,
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
