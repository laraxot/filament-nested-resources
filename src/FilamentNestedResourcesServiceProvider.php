<?php

namespace SevendaysDigital\FilamentNestedResources;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

<<<<<<< HEAD
=======

>>>>>>> 1f6734f (up)
class FilamentNestedResourcesServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-nested-resources';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }
}
