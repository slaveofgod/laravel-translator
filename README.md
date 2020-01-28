# Laravel Translations

The most time-consuming tasks when translating an application is to extract all the template contents to be translated and to keep all the translation files in sync. This package includes a command called `translation:update` that helps you with these tasks.

## Installation

```sh
$ composer require slaveofgod/laravel-translator dev-master
```

## Commands:
### Update Command:
Update translations from source code.
```sh
$ php artisan translation:update {locale} {--force} {--dump-messages}
```
Arguments:

 Name | Description | Default
:---------|:----------|:----------
 locale | The locale | - 
 path | Directory where to extract the messages | views 
 
Options:

 Name | Description | Default
:---------|:----------|:----------
 force | Should the update be done | false
 dump-messages |  Should the messages be dumped in the console | false 
 no-backup | Should backup not be done | false
 clean | Should clean not found messages. But we will ignore next files: 'validation', 'auth', 'passwords', 'pagination' | false 
 prefix | Override the default prefix. | __,@lang,trans_choice,@choice,__ab,@lang_ab,trans_choice_ab,@choice_ab 

### Update Untracked Command:
Update translations with untracked messages.

If you want to be able to tracked untracked messages please use `__ab` and `trans_choice_ab`.

What is **untracked** message: `{{ __($message) }}` or `{{ trans_choice($message, 5, ['value' => 5]) }}`.

When you use `__ab`, `trans_choice_ab`, `@lang_ab` or `@choice_ab` function they will work the same way as `__`, `trans_choice``@lang` or `@choice`  plus loging all messages to the special log file.
```sh
$ php artisan translation:untracked {locale} {--force} {--dump-messages}
```
Arguments:

 Name | Description | Default
:---------|:----------|:----------
 locale | The locale | - 
 
Options:

 Name | Description | Default
:---------|:----------|:----------
 force | Should the update be done | false
 dump-messages |  Should the messages be dumped in the console | false 
 no-backup | Should backup not be done | false

### Diff Command:
Difference between translation files and source code messages.
```sh
php artisan translation:diff en
```
Arguments:

 Name | Description | Default
:---------|:----------|:----------
 locale | The locale | - 
 path | Directory where to extract the messages | views 
 
 Options:

 Name | Description | Default
:---------|:----------|:----------
 prefix | Override the default prefix. | __,@lang,trans_choice,@choice,__ab,@lang_ab,trans_choice_ab,@choice_ab

-------

if for any reason artisan can't find commands, you can register the provider manually on your `config/app.php` file:

```php
return [
    ...
    'providers' => [
        ...
        AB\Laravel\Translator\ServiceProvider::class,
        ...
    ]
]
```
