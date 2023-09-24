<?php

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\{ artisan };
use function PHPUnit\Framework\assertFileDoesNotExist;
use function PHPUnit\Framework\assertFileExists;

trait RegisterConfig
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('register-configuration')
            ->withConfig([
                $package->getConfigPath('one.php'),
                $package->getConfigPath('two.php'),
            ])
            ->withConfig($package->getConfigPath('extra/three.php'), false)
            ->preventDefaultConfig();
    }
}

uses(RegisterConfig::class);

it('register config', function () {
    expect(config('one.key'))
        ->toBe('one')
        ->and(config('two.key'))
        ->toBe('two')
        ->and(config('three.key'))
        ->toBe('three');
});

it('publish config files', function () {
    artisan('vendor:publish', ['--tag' => 'register-configuration-config'])->run();

    assertFileExists(config_path('one.php'));
    assertFileExists(config_path('two.php'));
    assertFileDoesNotExist(config_path('three.php'));
});
