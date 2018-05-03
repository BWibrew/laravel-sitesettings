<?php

namespace BWibrew\SiteSettings\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use BWibrew\SiteSettings\Traits\ManagesSettings;
use BWibrew\SiteSettings\Interfaces\Setting as SettingInterface;

class Scope extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the settings for this scope.
     */
    public function settings()
    {
        return $this->hasMany(Setting::class);
    }
}
