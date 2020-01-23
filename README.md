# Laravel Schematics

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mtolhuys/laravel-schematics.svg?style=flat-square)](https://packagist.org/packages/mtolhuys/laravel-schematics)
[![Build Status](https://img.shields.io/travis/mtolhuys/laravel-schematics/master.svg?style=flat-square)](https://travis-ci.org/mtolhuys/laravel-schematics)
[![Quality Score](https://img.shields.io/scrutinizer/g/mtolhuys/laravel-schematics.svg?style=flat-square)](https://scrutinizer-ci.com/g/mtolhuys/laravel-schematics)
[![Total Downloads](https://img.shields.io/packagist/dt/mtolhuys/laravel-schematics.svg?style=flat-square)](https://packagist.org/packages/mtolhuys/laravel-schematics)

This package will map your Eloquent models and methods calling their relations.
You can see a flowchart of the results visiting `{your-app}/schematics`

Example result: 
![](resources/images/schematics.gif)

This package is still in BETA stage. More features coming soon:

    - Form requests, migrations and models creation
    - Drag and drop relations
    - Migrate and rollback buttons
    - Model inspection
    
## Installation

You can install the package via composer:

```bash
composer require mtolhuys/laravel-schematics
```

## Usage
Run `php artisan vendor:publish` and visit `{your-app}/schematics`

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
