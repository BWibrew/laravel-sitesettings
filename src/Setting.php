<?php

namespace BWibrew\SiteSettings;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\AuthManager as Auth;
use Illuminate\Cache\CacheManager as Cache;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class Setting extends Model implements HasMedia
{
    use HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
        'scope',
        'updated_by',
    ];

    /**
     * Update current setting name.
     *
     * @param $name
     * @return $this
     */
    public function updateName($name)
    {
        $this->name = $this->parseScopeName($name)['name'];
        $this->scope = $this->parseScopeName($name)['scope'];
        $this->updated_by = app(Auth::class)->user()->id;
        $this->save();
        $this->refreshCache();

        return $this;
    }

    /**
     * Update current setting value.
     *
     * @param $value
     * @param $delete_media
     * @return $this
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function updateValue($value = null, $delete_media = false)
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
     * @param $scope
     * @return $this
     */
    public function updateScope($scope)
    {
        $this->scope = $scope;
        $this->save();
        $this->refreshCache();

        return $this;
    }

    /**
     * Remove scope from setting.
     *
     * @return $this
     */
    public function removeScope()
    {
        return $this->updateScope('default');
    }

    /**
     * Register a new setting with optional value.
     *
     * Authenticated user ID will be assigned to 'updated_by' column.
     *
     * @param $name
     * @param $value
     * @return $this|Model
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public static function register($name, $value = null)
    {
        $setting = new self;

        $setting->name = $setting->parseScopeName($name)['name'];
        $setting->value = $value;
        $setting->scope = $setting->parseScopeName($name)['scope'];
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
     * @param $name
     * @return mixed
     */
    public static function getValue($name)
    {
        return (new self)->getProperty('value', $name);
    }

    /**
     * Get all values in a scope.
     *
     * @param $scope
     * @return array|null
     */
    public static function getScopeValues($scope = 'default')
    {
        if (! config('sitesettings.use_scopes')) {
            return;
        }

        return (new self)->getSettings()
            ->where('scope', $scope)
            ->pluck('value', 'name')
            ->toArray();
    }

    /**
     * Get the 'updated_by' user ID.
     *
     * @param $name
     * @return int|null
     */
    public static function getUpdatedBy($name)
    {
        return (int) (new self)->getProperty('updated_by', $name);
    }

    /**
     * Get the 'updated_by' user ID for a scope.
     *
     * @param $scope
     * @return int|null
     */
    public static function getScopeUpdatedBy($scope = 'default')
    {
        return (int) (new self)->getScopeProperty('updated_by', $scope);
    }

    /**
     * Get the updated_at timestamp.
     *
     * @param $name
     * @return mixed
     */
    public static function getUpdatedAt($name)
    {
        return (new self)->getProperty('updated_at', $name);
    }

    /**
     * Get the updated_at timestamp for a scope.
     *
     * @param $scope
     * @return mixed|null
     */
    public static function getScopeUpdatedAt($scope = 'default')
    {
        return (new self)->getScopeProperty('updated_at', $scope);
    }

    /**
     * Parses scope name dot syntax.
     *
     * @param $name
     * @return array
     */
    protected function parseScopeName($name)
    {
        $name_parts = explode('.', $name);

        if (config('sitesettings.use_scopes') && count($name_parts) >= 2) {
            $scope = array_shift($name_parts);
            $name = implode('.', $name_parts);
        }

        return [
            'scope' => isset($scope) ? $scope : 'default',
            'name' => $name,
        ];
    }

    /**
     * Gets the property from the setting.
     *
     * @param string $property
     * @param $name
     *
     * @return mixed
     */
    protected function getProperty($property, $name)
    {
        return $this->getSettings()
            ->where('name', $this->parseScopeName($name)['name'])
            ->where('scope', $this->parseScopeName($name)['scope'])
            ->pluck($property)
            ->first();
    }

    /**
     * Gets the property from the most recently updated setting in a scope.
     *
     * @param string $property
     * @param string $scope
     *
     * @return mixed
     */
    protected function getScopeProperty($property, $scope)
    {
        if (! config('sitesettings.use_scopes')) {
            return;
        }

        return $this->getSettings()
            ->where('scope', $scope)
            ->sortBy('updated_at')
            ->pluck($property)
            ->first();
    }

    /**
     * Cache all Setting objects.
     */
    protected function refreshCache()
    {
        app(Cache::class)->forever('bwibrew.settings', self::get());
    }

    /**
     * Retrieve Setting models from the cache.
     * If the models aren't already in the cache then this
     * method will cache and then return them.
     *
     * @return Setting
     */
    protected function getSettings()
    {
        return app(Cache::class)->rememberForever('bwibrew.settings', function () {
            return self::get();
        });
    }
}
