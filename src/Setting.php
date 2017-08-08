<?php

namespace Jamin87\SiteSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Setting extends Model
{
    protected $fillable = [
        'name',
        'value',
        'updated_by',
    ];

    /**
     * Set the settings name.
     *
     * @param  string $value
     * @return void
     * @throws \Exception
     */
    public function setNameAttribute($value)
    {
        if ($value !== snake_case($value)) {
            throw new \Exception();
        }
        $this->attributes['name'] = $value;
    }

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

    public function updateName($name)
    {
        $this->name = $name;
        $this->save();

        return $this;
    }

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

    public static function getValue($name)
    {
        return Setting::where('name', $name)->pluck('value')->first();
    }

    public static function getUpdatedBy($name)
    {
        return Setting::where('name', $name)->pluck('updated_by')->first();
    }

    public static function getWhenUpdated($name)
    {
        return Setting::where('name', $name)->pluck('updated_at')->first();
    }
}
