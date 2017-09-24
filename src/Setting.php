<?php

namespace BWibrew\SiteSettings;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
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
     * Set the settings name after checking for naming styles.
     *
     * @param  string $name
     * @return void
     * @throws \Exception
     */
    public function setNameAttribute($name)
    {
        if (config('sitesettings.force_naming_style') && ! $this->followsNamingStyle($name)) {
            throw new \Exception('Setting name does not match naming style');
        }

        $this->attributes['name'] = $name;
    }

    /**
     * Update current setting name.
     *
     * @param $name
     * @param $user
     * @return $this
     */
    public function updateName($name, $user = null)
    {
        $user ?: $user = Auth::user();

        if ($parts = $this->parseScopeName($name)) {
            $this->name = $parts['name'];
            $this->scope = $parts['scope'];
        } else {
            $this->name = $name;
        }

        $this->updated_by = $user->id;
        $this->save();

        return $this;
    }

    /**
     * Update current setting value.
     *
     * @param $value
     * @param $user
     * @return $this
     */
    public function updateValue($value = null, $user = null)
    {
        $user ?: $user = Auth::user();

        $this->value = $value;
        $this->updated_by = $user->id;
        $this->save();

        if ($value instanceof UploadedFile) {
            $this->syncWithMediaLibrary($this->name, $value, $user);
        }

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

        return $this;
    }

    /**
     * Remove scope from setting.
     *
     * @return $this
     */
    public function removeScope()
    {
        return $this->updateScope(null);
    }

    /**
     * Register a new setting with optional value.
     *
     * Authenticated user ID will be assigned to 'updated_by' column.
     *
     * @param $name
     * @param $value
     * @param $user
     * @return $this|Model
     */
    public static function register($name, $value = null, $user = null)
    {
        $user ?: $user = Auth::user();
        $setting = new self;

        if ($parts = $setting->parseScopeName($name)) {
            $name = $parts['name'];
            $scope = $parts['scope'];
        }

        $setting->name = $name;
        $setting->value = $value;
        $setting->scope = isset($scope) ? $scope : 'default';
        $setting->updated_by = $user->id;
        $setting->save();

        if ($value instanceof UploadedFile) {
            $setting->syncWithMediaLibrary($name, $value, $user);
        }

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
     * @return \Illuminate\Support\Collection|null
     */
    public static function getScopeValues($scope = 'default')
    {
        if (! config('sitesettings.use_scopes')) {
            return;
        }

        return self::where('scope', $scope)->pluck('value');
    }

    /**
     * Get the 'updated_by' user ID.
     *
     * @param $name
     * @return mixed
     */
    public static function getUpdatedBy($name)
    {
        return (new self)->getProperty('updated_by', $name);
    }

    /**
     * Get the 'updated_by' user ID for a scope.
     *
     * @param $scope
     * @return mixed|null
     */
    public static function getScopeUpdatedBy($scope = 'default')
    {
        return (new self)->getScopeProperty('updated_by', $scope);
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
     * Assert the setting name follows the naming style.
     *
     * @param $name
     * @return bool
     */
    protected function followsNamingStyle($name)
    {
        $naming_styles = config('sitesettings.naming_styles');
        $follows_style = false;

        foreach ($naming_styles as $style) {
            switch ($style) {
                case in_array('snake_case', $naming_styles) && $name === snake_case($name):
                    $follows_style = true;
                    break;
                case in_array('camel_case', $naming_styles) && $name === camel_case($name):
                    $follows_style = true;
                    break;
                case in_array('kebab_case', $naming_styles) && $name === kebab_case($name):
                    $follows_style = true;
                    break;
                case in_array('studly_case', $naming_styles) && $name === studly_case($name):
                    $follows_style = true;
                    break;
            }
        }

        return $follows_style;
    }

    /**
     * Parses scope name dot syntax. e.g. 'scope.name'.
     *
     * @param $name
     * @return array|null
     */
    protected function parseScopeName($name)
    {
        $name_parts = explode('.', $name);

        if (! config('sitesettings.use_scopes') || count($name_parts) < 2) {
            return;
        } else {
            return [
                'scope' => array_shift($name_parts),
                'name' => implode('.', $name_parts),
            ];
        }
    }

    /**
     * Syncs associated media library item.
     *
     * @param $name
     * @param $value
     */
    protected function syncWithMediaLibrary($name, $value)
    {
        $this->addMedia($value)->usingName($name)->toMediaCollection();

        switch (config('sitesettings.media_value_type')) {
            case 'path':
                $this->value = $this->getMedia()->first()->getPath();
                $this->save();
                break;
            case 'url':
                $this->value = $this->getMedia()->first()->getUrl();
                $this->save();
                break;
            case 'file_name':
                $this->value = $this->getMedia()->first()->file_name;
                $this->save();
                break;
        }
    }

    protected function getProperty($property, $name)
    {
        $scope = 'default';

        if ($parts = $this->parseScopeName($name)) {
            $name = $parts['name'];
            $scope = $parts['scope'];
        }

        return $this->where([['name', $name], ['scope', $scope]])->pluck($property)->first();
    }

    protected function getScopeProperty($property, $scope)
    {
        if (! config('sitesettings.use_scopes')) {
            return;
        }

        return self::where('scope', $scope)
            ->orderBy('updated_at')
            ->pluck($property)
            ->first();
    }
}
