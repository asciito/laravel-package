<?php

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\artisan;

trait PackageUnRegisterConfigTest
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('unregister-package')
            ->withConfig([
                $package->getConfigPath('one.php'),
                $package->getConfigPath('two.php'),
            ])
            ->withConfig($package->getConfigPath('extra/three.php'), false)
            ->withConfig($package->getConfigPath('extra/four.php'))
            ->excludeConfig([
                $package->getConfigPath('one.php'),
                $package->getConfigPath('two.php'),
            ])
            ->preventDefaultConfig();
    }
}

uses(PackageUnRegisterConfigTest::class);

test('package has register config files manually', function () {
    expect($this->package)
        ->getPublishableConfig()
        ->toHaveCount(1)
        ->getRegisteredConfig()
        ->toHaveCount(2);
});

test('package has no default config files register', function () {
    expect(config())
        ->get('one.key')
        ->toBeNull()
        ->get('two.key')
        ->toBeNull()
        ->get('three.key')
        ->toBe('three')
        ->get('four.key')
        ->toBe('four');
});

it('publish just one config file', function () {
    expect(config_path('four.php'))
        ->not->toBeFile();

    artisan('vendor:publish', ['--tag' => 'unregister-package-config'])
        ->assertSuccessful();

    expect(config())
        ->get('three.key')
        ->toBe('three')
        ->get('four.key')
        ->toBe('four')
        ->and(config_path('three.php'))
        ->not->toBeFile()
        ->and(config_path('four.php'))
        ->toBeFile();
});
