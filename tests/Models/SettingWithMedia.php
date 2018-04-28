<?php

namespace BWibrew\SiteSettings\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use BWibrew\SiteSettings\Traits\ManagesSettingsWithMedia;
use BWibrew\SiteSettings\Interfaces\SettingWithMedia as SettingInterface;

class SettingWithMedia extends Model implements SettingInterface
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

    protected $table = 'settings';
}
