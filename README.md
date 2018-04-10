# Laravel Translations

The most time-consuming tasks when translating an application is to extract all the template contents to be translated and to keep all the translation files in sync. This pacheg includes a command called `translation:update` that helps you with these tasks.

## Installation

```sh
$ composer require alexeybob/laravel-translator:dev-master
```

## Commands:
### Update Command:
```sh
$ php artisan translation:update {locale} {--force} {--dump-messages}
```
Arguments:

 Name | Description | Default
:---------|:----------|:----------
 locale | The locale | - 
 path |  Directory where to load the messages | views 
 
Options:

 Name | Description | Default
:---------|:----------|:----------
 force | Should the update be done | false
 dump-messages |  Should the messages be dumped in the console | false 
 no-backup | Should backup not be done | false
 clean | Should clean not found messages | false 
 prefix | Override the default prefix. | __,@lang,trans_choice,@choice,__ab,trans_choice_ab 

### Update Command:
```sh
php artisan translation:diff en
```
Arguments:

 Name | Description | Default
:---------|:----------|:----------
 locale | The locale | - 
 path |  Directory where to load the messages | views 
 
 Options:

 Name | Description | Default
:---------|:----------|:----------
 prefix | Override the default prefix. | __,@lang,trans_choice,@choice,__ab,trans_choice_ab

-------

if for any reason artisan can't find commands, you can register the provider manually on your `config/app.php` file:

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
