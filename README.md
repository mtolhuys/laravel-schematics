<h1>
    <img align="left" align="bottom" width="40" height="40" src="resources/images/icons/icon.png" />
    Laravel Schematics
</h1>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mtolhuys/laravel-schematics.svg?style=flat-square)](https://packagist.org/packages/mtolhuys/laravel-schematics)
[![Build Status](https://img.shields.io/travis/mtolhuys/laravel-schematics/master.svg?style=flat-square)](https://travis-ci.org/mtolhuys/laravel-schematics)
[![Quality Score](https://img.shields.io/scrutinizer/g/mtolhuys/laravel-schematics.svg?style=flat-square)](https://scrutinizer-ci.com/g/mtolhuys/laravel-schematics)
[![Total Downloads](https://img.shields.io/packagist/dt/mtolhuys/laravel-schematics.svg?style=flat-square)](https://packagist.org/packages/mtolhuys/laravel-schematics)

This package allows you to make multiple **diagrams** of your Eloquent **models** and their **relations**.
It will help building them providing drag and drop relations, forms to **create** and **edit** your models and many options like
adding **resource controllers**, **form requests** and running **migrations** by the click of a button or (configurable) automatically.
It also will give you insights in the migrations you ran and the ones specified in your migrations folder.  

![Schematics Example](resources/images/readme/intro.png)

Its aim is to help you (and your team) get more grip on the models from a code perspective, 
the status of your migrations and build / edit them faster.

---
    
## Installation

You can install the package via composer:

```bash
composer require mtolhuys/laravel-schematics --dev
```

Run `php artisan schematics:install` which will do the route caching, create vendor assets in your public folder and
setup the configuration file.

Or...

```php
php artisan vendor:publish --provider="Mtolhuys\LaravelSchematics\LaravelSchematicsServiceProvider"
```

and visit `{your-app}/schematics`

Make sure the routes are cached!

---

## Usage

#### Starting out / Searching
If you already have a lot of models I recommend you use the search bar to narrow down the diagrams into 
specific sections f.e:

![Schematics Example](resources/images/readme/search-example.png)

These searches are saved across your diagrams.

<img width="800" src="resources/images/readme/search-example.gif" />

#### Relations
When you drag and drop the arrow to another model you'll get a form to specify the relation you want to build f.e:

<img width="800" src="resources/images/readme/relation-example.gif" />

Clicking on them will show something like:

![Relation Example](resources/images/readme/relation-example.png)

#### Building / Editing Models

To specify the types of your columns I've chosen the form request rule syntax. 
To see what's available you can click the little help icon below the fields in the model form:

![Fields Explanation](resources/images/readme/fields-explanation.png)

The package is flexible enough to differentiate between renaming and changing column types. 
It will create migrations according the need to change or create a table and setup your `$fillables`.
I do **strongly** suggest you check the migrations before running them if the stakes are high.

You don't have to worry about existing migrations though, the package will only look for migrations it created itself
by checking the value of the `@tag` in the comment it adds. It looks like this:

```php
/**
 * Laravel Schematics
 *
 * WARNING: removing @tag value will disable automated removal
 *
 * @package Laravel-schematics
 * @author  Maarten Tolhuijs <mtolhuys@protonmail.com>
 * @url     https://github.com/mtolhuys/laravel-schematics
 * @tag     laravel-schematics-foobar-model
 */
```

#### Changing Diagram Style

There are 4 diagram styles. Bezier, Straight, Flowchart and State Machine:

<img width="800" src="resources/images/readme/styles-example.gif" />

#### Importing / Exporting Diagrams

To save and / or share the diagrams you created you can use the export and import settings feature:

<img width="800" src="resources/images/readme/export-import-example.gif" />

---

### Testing

You can run the tests with:

```bash
composer test
```

Or, optionally, with coverage information:

```bash
composer test-coverage
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Credits

This package is possible because of the effort and time of these people! ✨

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
    <tr>
        <td align="center">
            <a href="https://github.com/mtolhuys">
                <img src="https://avatars1.githubusercontent.com/u/10849136?s=400&v=4" width="100px;" alt=""/><br/>
                <sub><b>Maarten Tolhuijs</b></sub>
            </a>
            <br/>
                <a>Creator️</a>
        </td>
        <td align="center">
            <a href="https://github.com/dtolhuijs">
                <img src="https://avatars0.githubusercontent.com/u/16704769?s=400&v=4" width="100px;" alt=""/><br/>
                <sub><b>Deisi Tolhuijs</b></sub>
            </a>
            <br/>
            <a>Design</a>
            <br/>
        </td>
        <td align="center">
            <a href="https://github.com/Roboroads">
                <img src="https://avatars3.githubusercontent.com/u/4398301?s=400&v=4" width="100px;" alt=""/><br/>
                <sub><b>Robbin Schepers</b></sub>
            </a>
            <br/>
            <a>Contributor</a>
            <br/>
        </td>
        <td align="center">
            <a href="https://github.com/bramvanrijswijk">
                <img src="https://avatars1.githubusercontent.com/u/26224522?s=400&v=4" width="100px;" alt=""/><br/>
                <sub><b>Bram van Rijswijk</b></sub>
            </a>
            <br/>
            <a>Support</a>
            <br/>
        </td>
        <td align="center">
            <a href="https://github.com/elbojoloco">
                <img src="https://avatars2.githubusercontent.com/u/7912315?s=400&v=4" width="100px;" alt=""/><br/>
                <sub><b>Lucas Snel</b></sub>
            </a>
            <br/>
            <a>Contributor</a>
            <br/>
        </td>
    </tr>
</table>

<!-- markdownlint-enable -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

## Contributing

Since I'm getting some questions about this I want these things to be perfectly clear:

- This is a **safe** haven for contributions, every (positive) contributon matters!
- You are free (and encouraged) to use **anything** of this package for your own ideas.
- You can **always** ask for help or email me directly for any questions.

Please see [CONTRIBUTING](CONTRIBUTING.md) for further details.

### Security

If you discover any security related issues, please email mtolhuys@protonmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
