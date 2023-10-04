<?php

namespace Asciito\LaravelPackage\Package;

use Asciito\LaravelPackage\Package\Concerns\HasCommand;
use Asciito\LaravelPackage\Package\Concerns\HasConfig;
use Asciito\LaravelPackage\Package\Concerns\HasInstallCommand;
use Asciito\LaravelPackage\Package\Concerns\HasMigration;
use Asciito\LaravelPackage\Package\Contracts\WithCommand;
use Asciito\LaravelPackage\Package\Contracts\WithConfig;
use Asciito\LaravelPackage\Package\Contracts\WithInstallCommand;
use Asciito\LaravelPackage\Package\Contracts\WithMigration;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class Package implements WithCommand, WithConfig, WithMigration, WithInstallCommand
{
    use HasCommand, HasConfig, HasMigration, HasInstallCommand;

    /**
     * @var string The package name
     */
    protected string $name = 'laravel-package';

    /**
     * @var string Base path for the package
     */
    protected string $basePath;

    /**
     * @var string Package namespace
     */
    protected string $namespace;

    public static array $register = [];

    public static array $excluded = [];

    /**
     * Set the package name
     *
     * @param  string  $name  The package name
     */
    public function setName(string $name): static
    {
        $this->name = str($name)->slug();

        return $this;
    }

    /**
     * Prefix the given value string with the package name
     * If the package name is the same as the given value, the same value is return
     */
    public function prefixWithPackageName(string $value, string $sep = '-'): string
    {
        if ($value === $this->name()) {
            return $value;
        }

        return $this->name().$sep.$value;
    }

    /**
     * The name of the package
     * If the name was not set, the name is guest by using your composer.json
     */
    public function name(): string
    {
        if (isset($this->name)) {
            return $this->name;
        }

        $composerFile = $this->getBasePath('../composer.json');

        if (! File::exists($composerFile)) {
            throw new FileNotFoundException('The composer.json is not');
        }

        $content = File::get($composerFile);

        $json = json_decode($content, true, JSON_THROW_ON_ERROR);

        $this->name = str($json['name'])->after('/')->slug();

        return $this->name;
    }

    public function getBasePath(string $path = ''): string
    {
        return join_paths($this->basePath, $path);
    }

    public function setBasePath(string $path): static
    {
        $this->basePath = rtrim($path, DIRECTORY_SEPARATOR);

        return $this;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): static
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function ensureRegistersInitialize($component): void
    {
        $key = $this->prefixWithPackageName($component, '.');

        data_fill(static::$excluded, $key, []);
        data_fill(static::$register, $key, []);
    }

    protected function getRegister(string $component): Collection
    {
        $key = $this->prefixWithPackageName($component, '.');

        return collect(data_get(static::$register, $key));
    }

    protected function register(string $component, mixed $data): static
    {
        $package = $this->name();

        static::$register[$package][$component] = array_merge(
            static::$register[$package][$component],
            Arr::wrap($data),
        );

        return $this;
    }

    protected function getExclude(string $component): Collection
    {
        $key = $this->prefixWithPackageName($component, '.');

        return collect(data_get(static::$excluded, $key));
    }

    protected function exclude(string $component, mixed $data): static
    {
        $package = $this->name();

        static::$excluded[$package][$component] = array_merge(
            static::$excluded[$package][$component],
            Arr::wrap($data),
        );

        return $this;
    }

    public function getFilesFrom(string $path): Collection
    {
        if (! File::exists($path)) {
            return collect();
        }

        return collect(File::files($path))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
            ->map(fn (SplFileInfo $file): string => $file);
    }

    public function makeMigrationName(string $migration): string
    {
        if (Str::isMatch('/\d{4}_\d{2}_\d{2}_\d{6}/', $migration)) {
            return $migration;
        }

        return Carbon::now()->format('Y_m_d_His_').trim($migration, '_');
    }
}
