<?php

namespace BWibrew\SiteSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Setting extends Model
{
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
        if (config('sitesettings.force_naming_style') && !$this->followsNamingStyle($name)) {
            throw new \Exception('Setting name does not match naming style');
        } else {
            $this->attributes['name'] = $name;
        }
    }

    /**
     * Update current setting name.
     *
     * @param $name
     * @param null $user
     * @return $this
     */
    public function updateName($name, $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

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
     * @param null $value
     * @param null $user
     * @return $this
     */
    public function updateValue($value = null, $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        $this->value = $value;
        $this->updated_by = $user->id;
        $this->save();

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
     * @param null $value
     * @param null $user
     * @return $this|Model
     */
    public static function register($name, $value = null, $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if ($parts = (new Setting)->parseScopeName($name)) {
            $name = $parts['name'];
            $scope = $parts['scope'];
        }

        return Setting::create([
            'name' => $name,
            'scope' => isset($scope) ? $scope : null,
            'value' => $value,
            'updated_by' => $user->id,
        ]);
    }

    /**
     * Get a setting value.
     *
     * @param $name
     * @return mixed
     */
    public static function getValue($name)
    {
        if ($parts = (new Setting)->parseScopeName($name)) {
            $name = $parts['name'];
        }

        return Setting::where('name', $name)->pluck('value')->first();
    }

    /**
     * Get all values in a scope.
     *
     * @param $scope
     * @return \Illuminate\Support\Collection|null
     */
    public static function getScopeValues($scope)
    {
        if (!config('sitesettings.use_scopes')) {
            return null;
        }

        return Setting::where('scope', $scope)->pluck('value');
    }

    /**
     * Get the 'updated_by' user ID.
     *
     * @param $name
     * @return mixed
     */
    public static function getUpdatedBy($name)
    {
        if ($parts = (new Setting)->parseScopeName($name)) {
            $name = $parts['name'];
        }

        return Setting::where('name', $name)->pluck('updated_by')->first();
    }

    /**
     * Get the 'updated_by' user ID for a scope.
     *
     * @param $scope
     * @return mixed|null
     */
    public static function getScopeUpdatedBy($scope)
    {
        if (!config('sitesettings.use_scopes')) {
            return null;
        }

        return Setting::where('scope', $scope)
                      ->orderBy('updated_at')
                      ->pluck('updated_by')
                      ->first();
    }

    /**
     * Get the updated_at timestamp.
     *
     * @param $name
     * @return mixed
     */
    public static function getWhenUpdated($name)
    {
        if ($parts = (new Setting)->parseScopeName($name)) {
            $name = $parts['name'];
        }

        return Setting::where('name', $name)->pluck('updated_at')->first();
    }

    /**
     * Get the updated_at timestamp for a scope.
     *
     * @param $scope
     * @return mixed|null
     */
    public static function getWhenScopeUpdated($scope)
    {
        if (!config('sitesettings.use_scopes')) {
            return null;
        }

        return Setting::where('scope', $scope)
            ->orderBy('updated_at')
            ->pluck('updated_at')
            ->first();
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
     * Parses scope name dot syntax. e.g. 'scope.name'
     *
     * @param $name
     * @return array|null
     */
    protected function parseScopeName($name)
    {
        $name_parts = explode('.', $name);

        if (!config('sitesettings.use_scopes') || count($name_parts) < 2) {
            return null;
        } else {
            return [
                'scope' => array_shift($name_parts),
                'name' => implode('.', $name_parts),
            ];
        }
    }
}
