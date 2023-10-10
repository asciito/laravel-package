# Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/asciito/laravel-package.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/asciito/laravel-package)
[![Licence on Packagist](https://img.shields.io/packagist/l/asciito/laravel-package.svg?label=Packagist%20License&style=flat-square)](https://packagist.org/packages/asciito/laravel-package)
[![Tests](https://img.shields.io/github/actions/workflow/status/asciito/laravel-package/run-tests.yml?label=Tests&style=flat-square)](https://github.com/asciito/laravel-package/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/asciito/laravel-package.svg?label=Downloads&style=flat-square)](https://packagist.org/packages/asciito/laravel-package)

This PHP package will help you to create Laravel packages way easier, at least
for me.

---

># :warning: This project is not ready for production :warning:

## Installation and usage

To install the package is as simple as run the command:

```shell
composer require asciito/laravel-package
```

In order to create a Laravel package, this package follows some conventions to find your files, so
we follow the Laravel folder structure, thats why you should follow this convention, to make your time and
effort work.

<br />

The following schema describes the folder structure of a package.
```
<your-package>
├─ src
│   ├─ Console
│   │   └─ Commands
│   │       └─ ...
│   └─ YourPackageServiceProvider.php
├─ config
│   └─ package-config.php
├─ database
│   └─ migrations
│       ├─ your_package_migration_table.php
│       ├─ other_package_migration_table.php
│       └─ ...
├─ test
│   └─ ...
├─ composer.json
├─ composer.lock
├─ README.md
├ ...
.
```

Laravel is made around service providers, so for us our entry point is a **Service Provider** in your ```src/``` folder, this
will ensure that we can find the ```commands```, ```config```, and ```migration``` files without any problem.


### ServiceProvider

Our first step is create a ServiceProvider, ```extend``` our class from ```Asciito\LaravelPackage\Package\PackageServiceProvider``` class, and implement the method
```PackageServiceProvider::configurePackage(Package $package)```, this method get and instance of ```Package```, and we can start to configure our package.

The most basic configuration possible for our package is setting just the package name, this will be used to register our publishable files.

```php
class LaravelPackageServiceProvider extends PackageServiceProvider
{
    protected function configurePackage(Package $package): void
    {
        $package->setName('<your-package-name>');
    }
}
```

> Keep in mind if you don't provide the package name, the package will use the name from the composer.json file, excluding
> only the **vendor** part. 
> 
> e.g.
> 
> **\<vendor>/<your-package>**
> 
> The package name would be **<your-package>**, only.
> 


### Register commands

To register your package commands, call the method ```static::withCommands(string|string[] $command)```. Calling this method will configure your package
to look for files on ```<your-package>/src/Console/Commands``` folder, and you don't need to do anything else.

```php
protected function configurePackage(Package $package): string
{
    $package
        ->setName('<your-package-name>')
        ->withCommands();
}
```

Now your commands are registered, you can call them from the artisan console as any other command.

### Register config files

To register your package config files, call the method ```static::withCommands(string|string[] $config = [], bool $publish = true)```. Calling this method will configure your package
to look for files on ```<your-package>/config``` folder, and you don't need to do anything else.

```php
protected function configurePackage(Package $package): string
{
    $package
        ->setName('<your-package>')
        ->withConfig();
}
```

Now your config files are registered, you can access them with the ```config()``` function as any other config file, but in addition, you can choose
to publish those config files from your folder by running the command ```php artisan vendor:publish --tag=<your-package>-config```, and that's it.

### Register migration files

To register your package migration files, call the method ```static::withMigration(string|string[] $migration = [], bool $publish = true)```. Calling this method will configure your package
to look for files on ```<your-package>/database/migrations``` folder, and you don't need to do anything else.

```php
protected function configurePackage(Package $package): string
{
    $package
        ->setName('<your-package-name>')
        ->withMigrations();
}
```

Now you can run your migrations by just calling the command ```php artisan migrate```, but in addition, you can choose to publish those migration files from your folder by running the command
```php artisan vendor:publish --tag=<your-package>-migrations```, and that's it. 

> Keep in mind that the package might now keep the order or your migrations, if you want to keep the order in your migrations, please
> follow the laravel convention for migration files because we load the files from the folder, and we don't sort them.
> 
> Laravel's migration file name convention is something like this **YYYY_MM_DD_HHMMSS_your_migration_table.php**

<br />

### What's next

This is just the tip of the iceberg, if you want to add more files from unconventional places, please
read the [Documentation](#documentation) for more information.

---
## Documentation

The documentation is an extensive manuscript of how to configure different components (config, migrations, and commands).

* [The Basics](#the-basics)
  * [What is a package?](#what-is-a-package)
  * [How to register a package](#how-to-register-a-package)
  * [Configure your package](#configure-your-package)
    * [Configuration Component](#configuration-component)
      * [Un-publish configuration](#un-publish-configuration)

### The basics

<details>

<summary id="what-is-a-package">
    <strong>What is a package?</strong>
</summary>

A package is a collection of components namespaced, so you can have more "packages" inside the same composer package. This can be kind of confusing, but this lets you separate a big project into small an self contain "packages". You will see this more in deep later.

</details>

<details>

<summary id="how-to-register-a-package">
    <strong>How to register a package</strong>
</summary>

Register a package it's easy, we need to create a service provider and extend the class ```PackageServiceProvider```, then, implement the method ```configurePackage(Package $package): void```. Finally give a name to your package calling the method ```setName(string $name): static``` from the object $package.

```php
use Asciito\LaravelPackage\Package\Package;
use Asciito\LaravelPackage\Package\PackageServiceProvider;

class YourPackageServiceProvider extends PackageServiceProvider
{
    protected function configurePackage(Package $package): void
    {
        $package->setName('<your-package-name>');
    }
}
```

> The ```$package``` parameter is an instance for this Package. A single package is created for every single ```ServiceProvider``` class that extends the ```PackageServiceProvider``` class, so be sure to give a unique name to your project.

That's it, you successfully register your package... almost, the more important part is to add this ```ServiceProvider``` class to Laravel. You can do this by just simply adding it to your composer file.

```json
{
    "extras": {
        "laravel": {
            "providers": [
                "\\Vendor\\YourPackageName\\YourPackageServiceProvider"      
            ]
        }
    }
}
```

> From Laravel 5.5 and above

Doing this will auto-discover your service provider, and now that's all, your package is fully register on Laravel.

</details>

<details>
<summary>
    <strong>Configure your package</strong>
</summary>

There are three ways to configure your package, and these are called **component**. Every component will configure one part of your package with files that can be uses directly in Laravel or by publishing it for user personalization.


#### Configuration Component

If you want to have config parameters available with the method ```config()```, and being able to publish those files, call the method ```withConfig(string|array $config = [], bool $publish = true)```.

```php
use Asciito\LaravelPackage\Package\Package;
use Asciito\LaravelPackage\Package\PackageServiceProvider;

class YourPackageServiceProvider extends PackageServiceProvider
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('<your-package-name>')
            ->withConfig();
    }
}
```

If you call this method without any parameter, this will only register those files in the default config folder of your package.

See the next example 👇

```
<your-package>
├─ src
│   └─ YourPackageServiceProvider.php
├─ config <------------------------------------ This folder
│   └─ package-config.php
```
> 💡 Remember, we follow the Laravel project structure

Also, if you want to register config files outside this folder, you can do that too, just add the absolute path
to the ```withConfig()``` method call, and you should be able to use it too.

```php
use Asciito\LaravelPackage\Package\Package;
use Asciito\LaravelPackage\Package\PackageServiceProvider;

class YourPackageServiceProvider extends PackageServiceProvider
{
    protected function configurePackage(Package $package): void
    {
        $package
            ->setName('<your-package-name>')
            ->withConfig('/this/is/an/absolute/path/to/a/config/file.php');
    }
}
```

or even better, you can use the ```basePath()``` method from your package instance to get the path to some file.
Something like this: ```$package->basePath('other/folder/file.php')```.

> The base path is calculated from where you define your Service provider that
extends the ```PackageServiceProvider```.

##### Un-publish Configuration

*Working*...

</details>

---
### Edit Folder Structure

_Working..._

---
### License

__laravel-package__ is open-sourced software licensed under the [MIT license](./LICENSE).
