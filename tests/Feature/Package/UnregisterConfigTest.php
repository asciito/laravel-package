<?php

namespace Asciito\LaravelPackage\Tests\Feature\Package;

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\artisan;
use function PHPUnit\Framework\assertFileDoesNotExist;

trait UnregisterConfigTest
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('unregister')
            ->withConfig($package->getConfigPath('extra/three.php'))
            ->unregisterConfig($package->getConfigPath('one.php'))
            ->unpublishConfig($package->getConfigPath('extra/three.php'));
    }
}

uses(UnregisterConfigTest::class);

test('un-register config', function () {
    artisan('vendor:publish', ['--tag' => 'unregister-config'])->run();

    expect(config('one.key'))
        ->toBeNull();

    assertFileDoesNotExist(config_path('one.php'));
});

test('un-publish config', function () {
    artisan('vendor:publish', ['--tag' => 'unregister-config'])->run();

    expect(config('three.key'))
        ->toBe('three');

    assertFileDoesNotExist(config_path('three.key'));
});
