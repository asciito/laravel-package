# Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/asciito/laravel-package.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/asciito/laravel-package)
[![Licence on Packagist](https://img.shields.io/packagist/l/asciito/laravel-package.svg?label=Packagist%20License&style=flat-square)](https://packagist.org/packages/asciito/laravel-package)
[![Tests](https://img.shields.io/github/actions/workflow/status/asciito/laravel-package/run-tests.yml?label=Tests&style=flat-square)](https://github.com/asciito/laravel-package/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/asciito/laravel-package.svg?label=Downloads&style=flat-square)](https://packagist.org/packages/asciito/laravel-package)

This PHP package will help you to create packages for Laravel more easily, at least
for me.

---
## Installation and usage

Just run the command: 
```shell
composer require asciito/laravel-package
```

Then, we need to follow just a couple of rules of how to organize our package folders. so
we will follow the Laravel convention.

<br />

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

> #### Note:
> 
> You can edit the structure of the folders following the [Edit Folder structure](#edit-folder-structure) section

Now, on your service provider, you should declare two methods to put your package to work. One is the method ```static::configurePackage(Package $package)```
and ```static::getNamespace()```.

This is the most basic configuration of a Service Provider, but this doesn't do much for you. If you want to
register your commands, config files, and migrations files you need to chain more method calls to the package.

```php
class LaravelPackageServiceProvider extends PackageServiceProvider
{
    protected function configurePackage(Package $package): void
    {
        $package->setName('<your-package-name>');
    }

    protected function getNamespace(): string
    {
        return __NAMESPACE__;
    }
}
```

### Register commands

To register your package commands call the method ```static::withCommands(string|string[] ...$command)```. Just for now
don't worry about the method signature, if you just call the method this will register the commands from the default
command folder ```<your-package-folder>/src/Console/Commands```.

```php
protected function configurePackage(Package $package): string
{
    $package
        ->setName('<your-package-name>')
        ->withCommands();
}
```

### Register config files

To add config files is the same as the last section, just call the method ```static::withConfig(string|array $config = [], bool $publish = false)```.
For now don't worry about the signature, the only important ting about is if you call the method
you would be able to load and publish all your config files.

```php
protected function configurePackage(Package $package): string
{
    $package
        ->setName('<your-package-name>')
        ->withConfig();
}
```

With this you should be able to publish all your config files just by calling the command ```php artisan vendor:publish --tag=<your-package-name>-config```
and that's it, all your config files from your package would be published, and as the last section,
your default folder should be ```<your-package-folder>/config```.

### Register migration files

To register our migration just call the method ```static::withMigrations(string|array $config = [], bool $publish = false)```. Just for now don't
worry about the method signature, if you just call the method this will register the commands from the default migrations folder ```<your-package-folder>/database/migrations```.

```php
protected function configurePackage(Package $package): string
{
    $package
        ->setName('<your-package-name>')
        ->withMigrations();
}
```

With this you should be able to publish all your migration files just by calling the command php ```artisan vendor:publish --tag=<your-package-name>-migrations``` and that's it, all your
migration files from your package would be published, and as the last section, your default folder should be ```<your-package-folder>/database/migrations```.

<br/>

> #### Note:
>
> Laravel doesn't discover your package by default, so you need to register manually your Service Providers (before v5.5)
> but you can instruct Laravel to auto-discover you package by adding your Service Provider in your ```composer.json``` to the property
> ```extra``` (v5.5 or later).
>
> Something like this:
> ```json
> {
>   "extra": {
>       "laravel": {
>           "providers": [
>               "Vendor\\YourPackageName\\YourPackageServiceProvider"
>           ]
>       }
>   }
> }
> ```
> If you add your Service Provider to this property, Laravel will auto-discover your package.
>
> You can learn more on [Laravel Official Documentation](https://laravel.com/docs/10.x/packages#package-discovery)

---
### Working on

- [x] Run migration from default folder if the files aren't published
- [ ] Register Views
- [ ] Register Components
- [ ] Scaffold Package

---
## Documentation

_Working..._

---
### Edit Folder Structure

_Working..._

---
### License

__laravel-package__ is open-sourced software licensed under the [MIT license](./LICENSE).
