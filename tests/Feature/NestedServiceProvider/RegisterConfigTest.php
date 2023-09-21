<?php

use Asciito\LaravelPackage\Package\Package;

use function Pest\Laravel\{ artisan };
use function PHPUnit\Framework\assertFileExists;

trait NestedServiceProviderWithConfig
{
    protected function configureNestedService(Package $package): void
    {
        $package
            ->setName('nested-service')
            ->setConfigPath($package->getBasePath('../../config/nested'))
            ->withConfig();

        expect($package)
            ->getConfigPath()
            ->toBe($package->getBasePath('../../config/nested'))
            ->getPublishableConfig()
            ->not()->toBeEmpty()
            ->each
            ->toMatch('/nested-\w+.php$/');
    }
}

uses(NestedServiceProviderWithConfig::class);

it('register config', function () {
    expect(config('nested-one.key'))
        ->toBe('nested-one')
        ->and(config('nested-two.key'))
        ->toBe('nested-two');
});

it('publish config files', function () {
    artisan('vendor:publish', ['--tag' => 'nested-service-config'])->run();

    assertFileExists(config_path('nested-one.php'));
    assertFileExists(config_path('nested-two.php'));
});
