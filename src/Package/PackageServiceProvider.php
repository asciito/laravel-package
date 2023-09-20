<?php

namespace Asciito\LaravelPackage\Package;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

abstract class PackageServiceProvider extends ServiceProvider
{
    protected Package $package;

    protected string $basePath;

    protected array $publishable = ['config', 'migrations', 'commands'];

    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->registeringPackage();

        $this->configurePackage($this->getPackage());

        if ($this->package->hasConfig()) {
            foreach ($this->package->getRegisteredConfig() as $config) {
                $this->mergeConfigFrom($config, basename($config, '.php'));
            }
        }

        $this->packageRegistered();
    }

    /**
     * Run after all the services have been registered
     */
    public function boot(): void
    {
        $this->bootingPackage();

        $this->publishPackage();

        $this->packageBooted();
    }

    /**
     * Get the instance of the Package
     */
    protected function getPackage(): Package
    {
        if (empty($this->package)) {
            $this->package = $this->makePackage();
        }

        return $this->package;
    }

    /**
     * Make a new package
     */
    protected function makePackage(): Package
    {
        $package = new Package;

        $package
            ->setBasePath($this->getBasePath())
            ->setConfigPath($package->getBasePath('../config'))
            ->setMigrationPath($package->getBasePath('../database/migrations'))
            ->setNamespace($this->getNamespace());

        return $package;
    }

    protected function publishPackage(): void
    {
        foreach ($this->publishable as $component) {
            $this->{'publishes'.\Illuminate\Support\Str::title($component)}($this->package);
        }
    }

    /**
     * Run before booting the package
     */
    protected function bootingPackage(): void
    {
        //
    }

    /**
     * Run after the package has been booted
     */
    protected function packageBooted(): void
    {
        //
    }

    /**
     * Run before registering the package
     */
    protected function registeringPackage(): void
    {
        //
    }

    /**
     * Run after the package has been registered
     */
    protected function packageRegistered(): void
    {
        //
    }

    /**
     * Configure the package
     */
    abstract protected function configurePackage(Package $package): void;

    protected function publishesConfig(Package $package): void
    {
        if ($package->hasConfig()) {
            $this->publishes(
                $this->package->getPublishableConfig()
                    ->mapWithKeys(fn (string $config) => [$config => config_path(basename($config))])
                    ->all(),
                $package->prefixWithPackageName('config'),
            );
        }
    }

    /**
     * Boot the migrations
     */
    protected function publishesMigrations(Package $package): void
    {
        if ($package->hasMigrations()) {
            $this->publishes(
                $package->getPublishableMigrations()
                    ->mapWithKeys(function (string $migration, int|string $index) {
                        if (preg_match('/^\d{4}_\d{2}_\d{2}_/', $migration)) {
                            return $migration;
                        }

                        $date = Carbon::now()->format('Y_m_d');

                        $index = str($index)->padLeft(6, '0');

                        return [
                            $migration => database_path('migrations/'.$date.'_'.$index.'_'.basename($migration)),
                        ];
                    })
                    ->all(),
                $package->prefixWithPackageName('migrations')
            );
        }
    }

    /**
     * Boot the commands
     */
    protected function publishesCommands(Package $package): void
    {
        if ($package->hasCommands()) {
            $this->commands($package->getRegisteredCommands()->all());
        }
    }

    /**
     * The base path from with in the ServiceProvider has been defined
     */
    protected function getBasePath(): string
    {
        if (empty($this->basePath)) {
            $filename = (new \ReflectionClass($this))->getFileName();

            $this->basePath = pathinfo($filename, PATHINFO_DIRNAME);
        }

        return $this->basePath;
    }

    abstract protected function getNamespace(): string;
}
