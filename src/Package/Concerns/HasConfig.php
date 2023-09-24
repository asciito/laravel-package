<?php

namespace Asciito\LaravelPackage\Package\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

trait HasConfig
{
    protected string $configPath;

    protected array $configFiles = [];

    protected bool $shouldIncludeConfigFromFolder = false;

    protected array $excludeConfig = [];

    protected array $unpublishConfig = [];

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
        $config = [];

        if ($this->shouldLoadDefaultConfigFolder()) {
            $config = $this->loadConfigFilesFromFolder();
        }

        $register = collect($this->configFiles)
            ->merge($config)
            ->filter(fn (bool $_, string $config) => ! in_array($config, $this->excludeConfig))
            ->keys();

        return $register;
    }

    public function getPublishableConfig(): Collection
    {
        $files = [];

        if ($this->shouldLoadDefaultConfigFolder()) {
            $files = $this->loadConfigFilesFromFolder();
        }

        return collect($this->configFiles)
            ->merge([...$files, ...$this->unpublishConfig])
            ->filter(fn (bool $publish, string $config) => ! in_array($config, $this->excludeConfig))
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
        $this->excludeConfig[] = $path;

        return $this;
    }

    public function unpublishConfig(string $path): static
    {
        $this->unpublishConfig[$path] = false;

        return $this;
    }

    private function shouldLoadDefaultConfigFolder(): bool
    {
        return ! $this->preventLoadDefault && $this->shouldIncludeConfigFromFolder;
    }

    private function loadConfigFilesFromFolder(): array
    {
        $files = File::files($this->getConfigPath());

        return collect($files)
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
            ->mapWithKeys(fn (SplFileInfo $file) => [(string) $file => true])
            ->all();
    }
}
