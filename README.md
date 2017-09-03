# Persistent CMS style site settings for Laravel

*** **Currently under active development** ***

**Todo:**
- Add support for media uploads

[![Latest Version on Packagist](https://img.shields.io/packagist/v/BWibrew/laravel-sitesettings.svg?style=flat-square)](https://packagist.org/packages/BWibrew/laravel-sitesettings)
[![Build Status](https://img.shields.io/travis/BWibrew/laravel-sitesettings.svg?branch=master&style=flat-square)](https://travis-ci.org/BWibrew/laravel-sitesettings)
[![StyleCI](https://styleci.io/repos/99725839/shield?branch=master)](https://styleci.io/repos/99725839)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/BWibrew/laravel-sitesettings.svg?style=flat-square)](https://scrutinizer-ci.com/g/BWibrew/laravel-sitesettings)

## Installation
Install using Composer by running:
```
composer require BWibrew/laravel-sitesettings
```

Then add the service provider to the providers array in `config/app.php`:
```php
'providers' => [
    ...
    BWibrew\SiteSettings\SiteSettingsServiceProvider::class,
];
```

Then run table migrations with:
```
php artisan migrate
```

Publish the config file with:
```
php artisan vendor:publish --provider="BWibrew\SiteSettings\SiteSettingsServiceProvider" ==tag="config"
```

Publish the migrations with:
```
php artisan vendor:publish --provider="BWibrew\SiteSettings\SiteSettingsServiceProvider" --tag="migrations"
```

## Usage
This package provides access to the `Setting` eloquent model with the following methods:

### Registering a new setting
A setting is created like this:

```php
Setting::register('homepage_title');
```
    
You can also assign a value when creating a setting:

```php
Setting::register('homepage_title', 'Laravel Site Settings');
```

You can then retrieve the value of the setting:

```php
Setting::getValue($name);
```

### Updating a setting
To update an existing setting:

```php
$setting = Setting::where('name', 'setting_name')->first();

// Update setting value
$setting->updateValue('A new title');

// Update setting name
$setting->updateName('new_name');
```

You can also retrieve the ID of the user whom last updated the setting and when the update was made.

```php
Setting::getUpdatedBy($name); // Returns user ID

Setting::getWhenUpdated($name); // Returns Carbon date object
```

### Using scopes
You can categories your settings into 'scopes'. Scopes use a simple 'dot' syntax on the setting name.

To assign or retrieve a setting in a scope, place the scope name in front of the setting name and separate them with a dot ('.'). e.g. `scope_name.setting_name`.
This works the same way with all methods which take a setting name as a parameter.

There are also a number of extra methods used with scopes:

```php
// Return an array of all values from a scope
Setting::getScopeValues();

// Return the user ID of the user which last updated a setting in a scope
Setting::getScopeUpdatedBy();

// Return when the most recent update was made in a scope
Setting::whenScopeUpdated();
```

To update or remove a scope:
```php
$setting = Setting::where('name', 'setting_name')->first();

$setting->updateScope('new_scope_name');

$setting->removeScope(); // Sets scope to null.
```


## Configuration
To enforce a naming style for your settings make sure `force_naming_style` is set to true in the config file.
Define which naming styles you wish to use in the `naming_styles` array. 
Currently `'snake_case'`, `'camel_case'`, `'kebab_case'` and `'studly_case'` are available.

To use scopes ensure that `use_scopes` is set to `true`

## Testing
Run tests with:
```
composer test
```
