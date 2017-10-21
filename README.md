# Persistent CMS style site settings for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/BWibrew/laravel-sitesettings.svg?style=flat-square)](https://packagist.org/packages/BWibrew/laravel-sitesettings)
[![Build Status](https://img.shields.io/travis/BWibrew/laravel-sitesettings.svg?branch=master&style=flat-square)](https://travis-ci.org/BWibrew/laravel-sitesettings)
[![StyleCI](https://styleci.io/repos/99725839/shield?branch=master)](https://styleci.io/repos/99725839)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/BWibrew/laravel-sitesettings.svg?style=flat-square)](https://scrutinizer-ci.com/g/BWibrew/laravel-sitesettings)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/BWibrew/laravel-sitesettings.svg?style=flat-square)](https://scrutinizer-ci.com/g/BWibrew/laravel-sitesettings)

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
php artisan vendor:publish --provider="BWibrew\SiteSettings\SiteSettingsServiceProvider" --tag="config"
```

Publish the migrations with:
```
php artisan vendor:publish --provider="BWibrew\SiteSettings\SiteSettingsServiceProvider" --tag="migrations"
```

### File Uploads
To support the ability to save uploaded files as settings you also need to install the spatie/laravel-medialibrary package.
Installation instructions can be found [here](https://github.com/spatie/laravel-medialibrary/tree/v5#installation).

Here is the minimum set-up needed:

Add the service provider to the providers array in `config/app.php`:
```php
'providers' => [
    ...
    Spatie\MediaLibrary\MediaLibraryServiceProvider::class,
];
```

Publish the migration with:
```
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
```

Then run table migrations with:
```
php artisan migrate
```

Add add a disk to `app/config/filesystems.php`. e.g:
```php
    ...
    'disks' => [
        ...

        'media' => [
            'driver' => 'local',
            'root'   => public_path().'/media',
        ],
    ...
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

Setting::getUpdatedAt($name); // Returns Carbon date object
```

### Using scopes
You can categories your settings into 'scopes'. Scopes use a simple dot syntax on the setting name.

To assign or retrieve a setting in a scope, place the scope name in front of the setting name and separate them with a 
dot: `scope_name.setting_name`.
This works the same way with all methods which take a setting name as a parameter.

There are also a number of extra methods used with scopes:

```php
// Return an array of all values from a scope
Setting::getScopeValues();

// Return the user ID of the user which last updated a setting in a scope
Setting::getScopeUpdatedBy();

// Return when the most recent update was made in a scope
Setting::getScopeUpdatedAt();
```

To update or remove a scope:
```php
$setting = Setting::where('name', 'setting_name')->first();

$setting->updateScope('new_scope_name');

$setting->removeScope(); // Sets scope to null.
```

### Usage with media uploads
This package makes use of the amazing [Spatie/MediaLibrary](https://github.com/spatie/laravel-medialibrary) to provide 
the ability to associate settings with uploaded media.

To use a file upload as a setting simply set the [file upload](https://laravel.com/docs/5.4/requests#files) as the 
setting value.

## Configuration
Set `use_scopes` to `false` to disable the use of scopes.

The `media_value_type` setting controls the value stored in the settings table. This can be set to `'file_name'`, 
`'path'` or `'url'`.

## Testing
Run tests with:
```
composer test
```
