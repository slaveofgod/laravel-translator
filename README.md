# Laravel-Translator

### Installation

```sh
$ composer require alexeybob/laravel-translator
```

Commands:
```sh
$ php artisan translation:extract
```

if for any reason artisan can't find `translation:extract` command, you can register the provider manually on your `config/app.php` file:

```php
return [
    ...
    'providers' => [
        ...
        Translator\TranslatorServiceProvider::class,
        ...
    ]
]
```