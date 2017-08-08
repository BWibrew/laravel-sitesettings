# Persistent CMS style site settings for Laravel

*** **Currently under active development** ***

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

## Testing
Run tests with:

    composer test
