<?php

namespace Asciito\LaravelPackage\Package\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasConfig
{
    protected string $configPath;

    protected bool $preventLoadDefaultConfigFolder = false;

    protected bool $shouldIncludeConfigFromFolder = false;

    public function hasConfig(): bool
    {
        return $this->shouldLoadDefaultConfigFolder()
            || $this->getRegister('config')->isNotEmpty();
    }

    public function withConfig(string|array $config = [], bool $publish = true): static
    {
        $this->ensureRegistersInitialize('config');

        if (filled($config)) {
            $this->register(
                'config',
                Arr::mapWithKeys(Arr::wrap($config), fn (string $path): array => [$path => $publish])
            );
        }

        $this->shouldIncludeConfigFromFolder = true;

        return $this;
    }

    public function excludeConfig(string|array $path): static
    {
        $this->exclude('config', Arr::wrap($path));

        return $this;
    }

    public function getRegisteredConfig(): Collection
    {
        $files = $this->getDefaultConfigFiles();

        return $files
            ->merge($this->getRegister('config'))
            ->keys()
            ->filter(fn (string $config) => ! in_array($config, $this->getExclude('config')->all()));
    }

    public function getPublishableConfig(): Collection
    {
        $files = $this->getDefaultConfigFiles();

        return $files->merge($this->getRegister('config'))
            ->filter(function (bool $publish, string $path) {
                $include = ! in_array($path, $this->getExclude('config')->all());

                return $include && $publish;
            })
            ->keys();
    }

    public function getConfigPath(string $path = ''): string
    {
        return join_paths($this->configPath, $path);
    }

    public function setConfigPath(string $path): static
    {
        $this->configPath = rtrim($path, DIRECTORY_SEPARATOR);

        return $this;
    }

    public function getDefaultConfigFiles(): Collection
    {
        if (! $this->shouldLoadDefaultConfigFolder()) {
            return collect();
        }

        return $this->getFilesFrom($this->configPath)
            ->mapWithKeys(fn (string $path) => [$path => true]);
    }

    public function preventDefaultConfig(): static
    {
        $this->preventLoadDefaultConfigFolder = true;

        return $this;
    }

    private function shouldLoadDefaultConfigFolder(): bool
    {
        return ! $this->preventLoadDefaultConfigFolder && $this->shouldIncludeConfigFromFolder;
    }
}
