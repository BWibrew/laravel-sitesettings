<?php

namespace Jamin87\SiteSettings;

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

        $setting = Setting::create([
            'name' => $name,
            'value' => $value,
            'updated_by' => $user->id,
        ]);

        return $setting;
    }

    /**
     * Update current setting name.
     *
     * @param $name
     * @return $this
     */
    public function updateName($name)
    {
        $this->name = $name;
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
     * Get a setting value.
     *
     * @param $name
     * @return mixed
     */
    public static function getValue($name)
    {
        return Setting::where('name', $name)->pluck('value')->first();
    }

    /**
     * Get the 'updated_by' user ID.
     *
     * @param $name
     * @return mixed
     */
    public static function getUpdatedBy($name)
    {
        return Setting::where('name', $name)->pluck('updated_by')->first();
    }

    /**
     * Get the updated_at timestamp.
     *
     * @param $name
     * @return mixed
     */
    public static function getWhenUpdated($name)
    {
        return Setting::where('name', $name)->pluck('updated_at')->first();
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
}
