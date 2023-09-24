<?php

namespace Asciito\LaravelPackage\Package;

use Asciito\LaravelPackage\Package\Concerns\HasCommands;
use Asciito\LaravelPackage\Package\Concerns\HasConfig;
use Asciito\LaravelPackage\Package\Concerns\HasMigrations;
use Asciito\LaravelPackage\Package\Contracts\WithCommands;
use Asciito\LaravelPackage\Package\Contracts\WithConfig;
use Asciito\LaravelPackage\Package\Contracts\WithMigrations;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class Package implements WithCommands, WithConfig, WithMigrations
{
    use HasCommands, HasConfig, HasMigrations;

    protected string $name;

    protected string $basePath;

    protected string $namespace;

    /**
     * The name of the package
     *
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

    /**
     * Set the name of the package
     *
     * @param  string  $name The name of the package
     */
    public function setName(string $name): static
    {
        $this->name = str($name)->slug();

        return $this;
    }

    /**
     * Prefix the given value string with the package name
     *
     * If the package name is the same as the given value, the same value is return
     */
    public function prefixWithPackageName(string $value, string $sep = '-'): string
    {
        if ($value === $this->name()) {
            return $value;
        }

        return $this->name().$sep.$value;
    }

    public function setBasePath(string $path): static
    {
        $this->basePath = rtrim($path, DIRECTORY_SEPARATOR);

        return $this;
    }

    public function getBasePath(string $path = ''): string
    {
        return join_paths($this->basePath, $path);
    }

    /**
     * Get the files from the given path
     *
     * @param  string|array  $extensions The extension of the files you want to include
     */
    protected function loadFilesFrom(string $path, string|array $extensions = 'php'): Collection
    {
        return collect(File::files($path))
            ->filter(fn (SplFileInfo $file) => in_array($file->getExtension(), (array) $extensions))
            ->mapWithKeys(fn (SplFileInfo $file) => [(string) $file => true]);
    }

    public function setNamespace(string $namespace): static
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
