<?php

namespace Asciito\LaravelPackage\Tests\Feature\Package;

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\artisan;
use function PHPUnit\Framework\assertFileDoesNotExist;

trait UnregisterConfig
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('unregistered-unpublished-configuration')
            ->withConfig($package->getConfigPath('extra/three.php'))
            ->unregisterConfig('one.php')
            ->unpublishConfig('three.php');
    }
}

uses(UnregisterConfig::class);

test('un-register config', function () {
    artisan('vendor:publish', ['--tag' => 'unregistered-unpublished-configuration-config'])->run();

    expect(config('one.key'))
        ->toBeNull();

    assertFileDoesNotExist(config_path('one.php'));
});

test('un-publish config', function () {
    artisan('vendor:publish', ['--tag' => 'unregistered-unpublished-configuration-config'])->run();

    assertFileDoesNotExist(config_path('three.key'));

    expect(config('three.key'))
        ->toBe('three');
});
