<?php

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\artisan;

trait PackageRegisterConfigTest
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('package')
            ->withConfig($package->getConfigPath('extra/three.php'))
            ->withConfig([$package->getConfigPath('extra/four.php')])
            ->withConfig();
    }
}

uses(PackageRegisterConfigTest::class);

test('package has registered files from folder', function () {
    expect($this->package)
        ->getPublishableConfig()
        ->toHaveCount(4)
        ->getRegisteredConfig()
        ->toHaveCount(4);
});

test('package register config files without publishing it', function () {
    expect(config_path('one.php'))
        ->not->toBeFile()
        ->and(config_path('two.php'))
        ->not->toBeFile()
        ->and(config_path('three.php'))
        ->not->toBeFile()
        ->and(config_path('four.php'))
        ->not->toBeFile()
        ->and(config())
        ->get('one.key')
        ->toBe('one')
        ->get('two.key')
        ->toBe('two')
        ->get('three.key')
        ->toBe('three')
        ->get('four.key')
        ->toBe('four');

});

it('publish package config files', function () {
    artisan('vendor:publish', ['--tag' => 'package-config'])->assertOk();

    expect(config_path('one.php'))
        ->toBeFile()
        ->and(config_path('two.php'))
        ->toBeFile()
        ->and(config_path('three.php'))
        ->toBeFile()
        ->and(config())
        ->get('one.key')
        ->toBe('one')
        ->get('two.key')
        ->toBe('two')
        ->get('three.key')
        ->toBe('three');
});
