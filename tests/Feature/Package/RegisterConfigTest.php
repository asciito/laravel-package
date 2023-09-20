<?php

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\{ artisan };
use function PHPUnit\Framework\assertFileExists;

trait PackageWithConfig
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('laravel-package')
            ->preventDefaultConfig()
            ->withConfig([
                $package->getConfigPath('one.php'),
                $package->getConfigPath('two.php'),
                $package->getConfigPath('extra/three.php'),
            ]);
    }
}

uses(PackageWithConfig::class);

it('register config', function () {
    expect(config('one.key'))
        ->toBe('one')
        ->and(config('two.key'))
        ->toBe('two')
        ->and(config('three.key'))
        ->toBe('three');
});

it('publish config files', function () {
    artisan('vendor:publish', ['--tag' => 'laravel-package-config'])
        ->doesntExpectOutputToContain('No publishable resources for tag [laravel-package-config]')
        ->assertSuccessful();

    assertFileExists(config_path('one.php'));
    assertFileExists(config_path('two.php'));
});
