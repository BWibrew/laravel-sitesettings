# Persistent CMS style site settings for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bwibrew/laravel-sitesettings.svg?style=flat-square)](https://packagist.org/packages/BWibrew/laravel-sitesettings)
[![CircleCI](https://img.shields.io/circleci/project/github/BWibrew/laravel-sitesettings.svg?style=flat-square)](https://circleci.com/gh/BWibrew/laravel-sitesettings)
[![StyleCI](https://styleci.io/repos/99725839/shield?branch=master)](https://styleci.io/repos/99725839)
[![Codacy grade](https://img.shields.io/codacy/grade/17b87061f0fa410d85ed63787e630f18.svg?style=flat-square)](https://www.codacy.com/app/BWibrew/laravel-sitesettings)
[![Codacy coverage](https://img.shields.io/codacy/coverage/17b87061f0fa410d85ed63787e630f18.svg?style=flat-square)](https://www.codacy.com/app/BWibrew/laravel-sitesettings)

## Support
This version supports Laravel 5.5 - 6 / PHP 7.2 and above.

## Installation
Install using Composer by running:
```
composer require BWibrew/laravel-sitesettings
```

Publish the config file with:
```
php artisan vendor:publish --provider="BWibrew\SiteSettings\SiteSettingsServiceProvider" --tag="config"
```

Publish the migrations with:
```
php artisan vendor:publish --provider="BWibrew\SiteSettings\SiteSettingsServiceProvider" --tag="migrations"
```

Then run table migrations with:
```
php artisan migrate
```

### Configuring your models
Add the following interface, trait and `$fillable` attribute to your `Setting` model:
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use BWibrew\SiteSettings\Traits\ManagesSettings;
use BWibrew\SiteSettings\Interfaces\SettingInterface;

class Setting extends Model implements SettingInterface
{
    use ManagesSettings;
    
    protected $fillable = [
        'name',
        'value',
        'updated_by',
    ];
    
    //
}
```

If using scopes then also add the following interface, trait and `$fillable` attribute to your `Scope` model:
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use BWibrew\SiteSettings\Interfaces\ScopeInterface;
use BWibrew\SiteSettings\Traits\ManagesSettingScopes;

class Scope extends Model implements ScopeInterface
{
    use ManagesSettingScopes;
    
    protected $fillable = [
        'name',
    ];
    
    //
}
```

### File Uploads

To support the ability to save uploaded files as settings you also need to install the `spatie/laravel-medialibrary` 
package.
```
composer require spatie/laravel-medialibrary
```
Full installation instructions can be found [here](https://github.com/spatie/laravel-medialibrary/tree/v7#installation).

**Here is the minimum set-up needed:**

Publish the migration with:
```
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
```

Then run table migrations with:
```
php artisan migrate
```

Add a disk to `app/config/filesystems.php`. e.g:
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

Lastly, you will need to use `SettingWithMediaInterface` and `ManagesSettingsWithMedia` instead 
of `SettingInterface` and `ManagesSettings` on your `Setting` model.

## Usage
This package provides a convenient API for using the settings with an existing Eloquent Model.

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

$setting->removeScope();
```

### Usage with file uploads
This package can make use of the amazing [Spatie/MediaLibrary](https://github.com/spatie/laravel-medialibrary) to 
provide the ability to associate settings with uploaded media.

To use a file upload as a setting simply set the [file upload](https://laravel.com/docs/5.5/requests#files) as the 
setting value.

Example:
```php
$file = $request->file('avatar');

$setting = Setting::register('avatar', $file);
```

The value returned on a file upload setting is a string controlled by the `file_value_type` config value.

## Configuration
Set `use_scopes` to `false` to disable the use of scopes.

The `file_value_type` setting controls the value stored in the settings table. This can be set to `'file_name'`, 
`'path'` or `'url'`.

## Testing
Run tests with:
```
composer test
```
