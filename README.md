# Doc Make Command

This is a laravel artisan that generates autocomplete files in a similar way to [IDE Helper](https://github.com/barryvdh/laravel-ide-helper).
Instead of generating one file, it creates a directory and copies the classes, functions, and docblocks into separate files for each class.
This works well with the [vim-php-namespace](https://github.com/arnaud-lb/vim-php-namespace) plugin for instance.

## Installation

1. Install via [composer](http://getcomposer.org): `composer require --dev terrence-howard/doc-make:dev-master`
2. Add the service provider to your `app/config/app.php` in the `providers` area: `'TerrenceHoward\DocMake\DocMakeServiceProvider'`

## Usage

Call the command from within laravel with `php artisan doc:make`. It will generate dummy classes in the `app/autocomplete` directory.
You can change the directory it saves to with `--path=path/to/autocomplete`. Remember to put that directory in your `.gitignore`!

## Respect

This class was originally created by [Terrence Howard](https://github.com/chemisus). All credit goes to him.
