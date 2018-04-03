# Laravel-Translator

### Installation

```sh
$ composer require alexeybob/laravel-translator:dev-master
```

Commands:
```sh
$ php artisan translation:update {locale} {--force} {--dump-messages}
```
*locale*: The locale
*path*: Directory where to load the messages, defaults to views folder
*--force*: Should the update be done
*--dump-messages*: Should the messages be dumped in the console
*--output-format*: Override the default output format. Default "json"
*--no-backup*: Should backup be disabled
*--clean*: Should clean not found messages
*--prefix: Override the default prefix. Default "__,@lang,trans_choice,@choice"



if for any reason artisan can't find `translation:update` command, you can register the provider manually on your `config/app.php` file:

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
