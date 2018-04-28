<?php

namespace BWibrew\SiteSettings\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use BWibrew\SiteSettings\Interfaces\SettingWithMedia;
use BWibrew\SiteSettings\Traits\ManagesSettingsWithMedia;

class Setting extends Model implements SettingWithMedia
{
    use ManagesSettingsWithMedia;

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
}
