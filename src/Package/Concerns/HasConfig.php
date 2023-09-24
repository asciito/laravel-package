<?php

namespace Asciito\LaravelPackage\Package\Concerns;

use Illuminate\Support\Collection;

trait HasConfig
{
    protected string $configPath;

    protected array $configFiles = [];

    protected bool $shouldIncludeConfigFromFolder = false;

    protected array $excludedConfig = [];

    protected array $unpublishedConfig = [];

    public function hasConfig(): bool
    {
        return $this->shouldLoadDefaultConfigFolder() || filled($this->configFiles);
    }

    public function withConfig(string|array $config = [], bool $publish = true): static
    {
        if (filled($config)) {
            $this->configFiles = collect($config)
                ->mapWithKeys(fn (string $value) => [$value => $publish])
                ->merge($this->configFiles)
                ->all();
        }

        $this->shouldIncludeConfigFromFolder = true;

        return $this;
    }

    public function preventDefaultConfig(): static
    {
        $this->preventLoadDefault = true;

        return $this;
    }

    public function getRegisteredConfig(): Collection
    {
        $config = $this->loadConfigDefaultFolder();

        return collect($this->configFiles)
            ->merge($config)
            ->filter(function (bool $_, string $config) {
                return ! in_array($config, $this->excludedConfig);
            })
            ->keys();
    }

    public function getPublishableConfig(): Collection
    {
        $config = $this->loadConfigDefaultFolder();

        return collect($this->configFiles)
            ->merge($config)
            ->filter(function (bool $publish, string $config) {
                return $publish && ! in_array($config, [...$this->excludedConfig, ...$this->unpublishedConfig]);
            })
            ->keys();
    }

    public function setConfigPath(string $path): static
    {
        $this->configPath = rtrim($path, DIRECTORY_SEPARATOR);

        return $this;
    }

    public function getConfigPath(string $path = ''): string
    {
        return join_paths($this->configPath, $path);
    }

    public function unregisterConfig(string $path): static
    {
        $this->excludedConfig[] = absolute($path, $this->getConfigPath());

        return $this;
    }

    public function unpublishConfig(string $path): static
    {
        $this->unpublishedConfig[] = absolute($path, $this->getConfigPath());

        return $this;
    }

    private function shouldLoadDefaultConfigFolder(): bool
    {
        return ! $this->preventLoadDefault && $this->shouldIncludeConfigFromFolder;
    }

    private function loadConfigDefaultFolder(): array
    {
        if (! $this->shouldLoadDefaultConfigFolder()) {
            return [];
        }

        return $this->loadFilesFrom($this->getConfigPath())->all();
    }
}
