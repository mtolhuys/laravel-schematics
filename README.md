<img align="left" width="80" height="80" src="resources/images/icons/icon_2.png" />

# Laravel Schematics

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mtolhuys/laravel-schematics.svg?style=flat-square)](https://packagist.org/packages/mtolhuys/laravel-schematics)
[![Build Status](https://img.shields.io/travis/mtolhuys/laravel-schematics/master.svg?style=flat-square)](https://travis-ci.org/mtolhuys/laravel-schematics)
[![Quality Score](https://img.shields.io/scrutinizer/g/mtolhuys/laravel-schematics.svg?style=flat-square)](https://scrutinizer-ci.com/g/mtolhuys/laravel-schematics)
[![Total Downloads](https://img.shields.io/packagist/dt/mtolhuys/laravel-schematics.svg?style=flat-square)](https://packagist.org/packages/mtolhuys/laravel-schematics)

This package allows you to make multiple diagrams of your Eloquent models and their relations.
It will help building them providing drag and drop relations, forms to create and edit your models and many options like
adding resource controllers, running migrations by the click of a button or (configurable) automatically.
It also will give you insights in the migrations you ran and the ones specified in your migrations folder.  

### Example:

![Schematics Example](resources/images/intro.png)
    
## Installation

You can install the package via composer:

```bash
composer require mtolhuys/laravel-schematics
```

## Usage
Run `php artisan schematics:install` which will do everything necessary.

Or...

```php
php artisan vendor:publish --provider="Mtolhuys\LaravelSchematics\LaravelSchematicsServiceProvider"`
```

and visit {your-app}/schematics

Make sure the routes are cached!

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email mtolhuys@protonmail.com instead of using the issue tracker.

## Credits

- [Maarten Tolhuijs](https://github.com/mtolhuys)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
