<?php

namespace BWibrew\SiteSettings\Traits;

use BWibrew\SiteSettings\Interfaces\ScopeInterface;
use BWibrew\SiteSettings\Interfaces\SettingInterface;
use Illuminate\Auth\AuthManager as Auth;
use Illuminate\Cache\CacheManager as Cache;
use Illuminate\Http\UploadedFile;

trait ManagesSettings
{
    /**
     * Get the scope that owns the setting.
     */
    public function scope()
    {
        return $this->belongsTo(app(ScopeInterface::class));
    }

    /**
     * Update current setting name.
     *
     * @param string $name
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function updateName(string $name)
    {
        $this->name = $this->parseScopeName($name)['name'];
        $this->scope_id = $this->parseScopeName($name)['scope_id'];
        $this->updated_by = app(Auth::class)->user()->id;
        $this->save();
        $this->refreshCache();

        return $this;
    }

    /**
     * Update current setting value.
     *
     * @param $value
     * @param bool $delete_media
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function updateValue($value = null, bool $delete_media = false)
    {
        $this->value = $value;
        $this->updated_by = app(Auth::class)->user()->id;
        $this->save();

        if ($value instanceof UploadedFile) {
            $this->syncWithMediaLibrary($this->name, $value);
        } elseif ($delete_media) {
            $this->getMedia()->first()->delete();
            $this->value = null;
            $this->save();
        }

        $this->refreshCache();

        return $this;
    }

    /**
     * Update a scope.
     *
     * @param string|null $scope
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function updateScope($scope = null)
    {
        $this->scope_id = $scope === null ? 0 : app(ScopeInterface::class)->firstOrCreate(['name' => $scope])->id;
        $this->save();
        $this->refreshCache();

        return $this;
    }

    /**
     * Remove scope from setting.
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function removeScope()
    {
        return $this->updateScope();
    }

    /**
     * Register a new setting with optional value.
     *
     * Authenticated user ID will be assigned to 'updated_by' column.
     *
     * @param string $name
     * @param $value
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public static function register(string $name, $value = null)
    {
        $setting = new self();

        $setting->name = $setting->parseScopeName($name)['name'];
        $setting->value = $value;
        $setting->scope_id = $setting->parseScopeName($name)['scope_id'];
        $setting->updated_by = app(Auth::class)->user()->id;
        $setting->save();

        if ($value instanceof UploadedFile) {
            $setting->syncWithMediaLibrary($name, $value);
        }

        $setting->refreshCache();

        return $setting;
    }

    /**
     * Get a setting value.
     *
     * @param string $name
     * @return mixed
     */
    public static function getValue(string $name)
    {
        return (new self())->getProperty('value', $name);
    }

    /**
     * Get all values in a scope.
     *
     * @param string|null $scope
     *
     * @return array|null
     */
    public static function getScopeValues($scope = null): ?array
    {
        if (! config('sitesettings.use_scopes')) {
            return null;
        }

        $scope_id = $scope === null ? 0 : app(ScopeInterface::class)->where('name', $scope)->first()->id;

        return (new self())->getSettings()
                         ->where('scope_id', $scope_id)
                         ->pluck('value', 'name')
                         ->toArray();
    }

    /**
     * Get the 'updated_by' user ID.
     *
     * @param string $name
     * @return int|null
     */
    public static function getUpdatedBy(string $name): ?int
    {
        return (int) (new self())->getProperty('updated_by', $name);
    }

    /**
     * Get the 'updated_by' user ID for a scope.
     *
     * @param string|null $scope
     * @return int|null
     */
    public static function getScopeUpdatedBy($scope = null): ?int
    {
        return (new self())->getScopeProperty('updated_by', $scope);
    }

    /**
     * Get the updated_at timestamp.
     *
     * @param string $name
     * @return mixed
     */
    public static function getUpdatedAt(string $name)
    {
        return (new self())->getProperty('updated_at', $name);
    }

    /**
     * Get the updated_at timestamp for a scope.
     *
     * @param string|null $scope
     * @return mixed|null
     */
    public static function getScopeUpdatedAt($scope = null)
    {
        return (new self())->getScopeProperty('updated_at', $scope);
    }

    /**
     * Parses scope name dot syntax.
     *
     * @param string $name
     * @return array
     */
    protected function parseScopeName(string $name): array
    {
        $name_parts = explode('.', $name);

        if (config('sitesettings.use_scopes') && count($name_parts) >= 2) {
            $scope = array_shift($name_parts);
            $name = implode('.', $name_parts);
        }

        return [
            'scope_id' => isset($scope) ? app(ScopeInterface::class)->firstOrCreate(['name' => $scope])->id : 0,
            'name' => $name,
        ];
    }

    /**
     * Gets the property from the setting.
     *
     * @param string $property
     * @param string $name
     *
     * @return mixed
     */
    public function getProperty(string $property, string $name)
    {
        return $this->getSettings()
                    ->where('name', $this->parseScopeName($name)['name'])
                    ->where('scope_id', $this->parseScopeName($name)['scope_id'])
                    ->pluck($property)
                    ->first();
    }

    /**
     * Gets the property from the most recently updated setting in a scope.
     *
     * @param string $property
     * @param string|null $scope
     *
     * @return mixed
     */
    public function getScopeProperty(string $property, $scope = null)
    {
        if (! config('sitesettings.use_scopes')) {
            return;
        }

        if ($scope === null) {
            return $this->getSettings()
                        ->where('scope_id', 0)
                        ->sortBy('updated_at')
                        ->pluck($property)
                        ->first();
        }

        return $this->getSettings()
                    ->reject(function (SettingInterface $setting) use ($scope) {
                        if ($setting->scope === null) {
                            return true;
                        }

                        return $setting->scope->name !== $scope;
                    })
                    ->sortBy('updated_at')
                    ->pluck($property)
                    ->first();
    }

    /**
     * Cache all Setting objects.
     */
    public function refreshCache(): void
    {
        app(Cache::class)->forever('bwibrew.settings', self::with('scope')->get());
    }

    /**
     * Retrieve Setting models from the cache.
     * If the models aren't already in the cache then this
     * method will cache and then return them.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSettings()
    {
        return app(Cache::class)->rememberForever('bwibrew.settings', function () {
            return self::with('scope')->get();
        });
    }
}
