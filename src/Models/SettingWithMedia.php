<?php

namespace BWibrew\SiteSettings\Models;

use Illuminate\Database\Eloquent\Model;
use BWibrew\SiteSettings\Traits\ManagesSettingsWithMedia;
use BWibrew\SiteSettings\Interfaces\SettingWithMediaInterface;

class SettingWithMedia extends Model implements SettingWithMediaInterface
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
        'updated_by',
    ];

    protected $table = 'settings';
}
