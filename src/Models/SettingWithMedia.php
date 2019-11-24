<?php

namespace BWibrew\SiteSettings\Models;

use BWibrew\SiteSettings\Interfaces\SettingWithMediaInterface;
use BWibrew\SiteSettings\Traits\ManagesSettingsWithMedia;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';
}
