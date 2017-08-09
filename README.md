# Persistent CMS style site settings for Laravel

*** **Currently under active development** ***

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jamin87/laravel-sitesettings.svg?style=flat-square)](https://packagist.org/packages/jamin87/laravel-sitesettings)
[![Build Status](https://img.shields.io/travis/jamin87/laravel-sitesettings.svg?branch=master&style=flat-square)](https://travis-ci.org/jamin87/laravel-sitesettings)

## Todo:
- Add support for media uploads
- Integration with Laravel config

## Installation
Install using Composer by running:

    composer require jamin87/laravel-sitesettings

Then add the service provider to the providers array in `config/app.php`:

    'providers' => [
        ...
        Jamin87\SiteSettings\SiteSettingsServiceProvider::class,
    ];

Then run table migrations with:

    php artisan migrate

## Usage
This package provides access to the `Setting` eloquent model with the following methods:

### Registering a new setting
A setting is created like this:

    Setting::register('homepage_title');
    
You can also assign a value when creating a setting:

    Setting::register('homepage_title', 'Laravel Site Settings');

You can then retrieve the value of the setting:

    Setting::getValue($name);

### Updating a setting
To update an existing setting:

    $setting = Setting::where('name', 'homepage_title')->first();
    
    // Update setting value
    $setting->updateValue('A new title');
    
    // Update setting name
    $setting->updateName('new_name');

You can also retrieve the ID of the user whom last updated the setting and when the update was made.

    Setting::getUpdatedBy($name); // Returns user ID
    
    Setting::getWhenUpdated($name); // Returns Carbon date object
    
## Testing
Run tests with:

    composer test
