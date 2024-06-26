# Helpers to work with nested resources

This package adds support for nested resources in Filament.

It provides the base classes and column to provide the nested structure.

It currently is not that configurable and you need to follow naming conventions usually used by Laravel.

Demo:

https://user-images.githubusercontent.com/866743/230615762-af98d4ff-a285-4ad3-8964-bbb26b6d7a6e.mov

## Installation

You can install the package via composer:

```bash
composer require sevendays-digital/filament-nested-resources
```

## Usage

Currently you need to do a couple of changes to make this work. But you start of by creating a 
[filament resource](https://filamentphp.com/docs/2.x/admin/resources/getting-started#creating-a-resource)
(The parent resource should already exist at this point. The resource we are changing is the child one).

Once you have that, you will need to change the `Filament/Resources/ChildModelResource.php` to the `NestedResource`.

```php
use SevendaysDigital\FilamentNestedResources\Columns\ChildResourceLink;
use SevendaysDigital\FilamentNestedResources\NestedResource;

class ChildModelResource extends NestedResource
{
    public static function getParent(): string
    {
        return ParentModelResource::class;
    }
}
```

Then for each of the resource pages, you need to add the trait:
```php
use SevendaysDigital\FilamentNestedResources\ResourcePages\NestedPage;
```

Finally, on your `ParentModelResource` you can add the column to provide the links:

```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            ChildResourceLink::make(ChildModelResource::class),
        ]);
}
```

### Many To Many Relationships

If you are using a many to many relationship, then you must write the inverse relationship inside your child model,
and then a special scope to retrieve it's parent.

Example with MorphToMany relation:

```php

/* Category.php  (parent) */

public function products(): MorphToMany
{
    $pivot_class = ProductMorph::class;
    $pivot = app($pivot_class);
    $pivot_table = $pivot->getTable();
    $pivot_fields = $pivot->getFillable();


    return $this->morphToMany(Product::class, 'model', $pivot_table)
        ->using($pivot_class)
        ->withPivot($pivot_fields)
        ->withTimestamps();
}

/* Product.php (child) */

//inverse relation
public function categories(): MorphToMany
{
    $pivot_class = ProductMorph::class;
    $pivot = app($pivot_class);
    $pivot_table = $pivot->getTable();
    $pivot_fields = $pivot->getFillable();


    return $this->morphedByMany(Category::class, 'model', $pivot_table)
        ->using($pivot_class)
        ->withPivot($pivot_fields)
        ->withTimestamps();
}

// This scope is very important to retreive the parent model 
public function scopeOfCategory($query,$parent)
{
    return $query->whereHas('categories', function ($query) use($parent) {
        $query->where('categories.id', $parent);
    });
}
```

### Accessing the parent

When you need the parent in livewire context such as the form, you can add the second argument to your form method:

```php
public static function form(Form $form, ?Event $parent = null): Form;
```

Where `Event` is the model that should be the parent.

### Sidebar

By default when in a "context" the sidebar will register the menu item for that resource.

So if you are inside a Project which has documents, the sidebar will show documents when you are on a project or deeper
level.

If you do not want this, you can set `shouldRegisterNavigationWhenInContext` to false in the child resource.

### Notes

Just make sure you set a custom slug for the resources so that it builds unique routes.

https://filamentphp.com/docs/2.x/admin/resources/getting-started#customizing-the-url-slug

## Testing

There's none :).

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Harings Rob](https://github.com/Sevendays-Digital)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
