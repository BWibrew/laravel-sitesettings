<?php

namespace BWibrew\SiteSettings\Models;

use BWibrew\SiteSettings\Interfaces\SettingInterface;
use BWibrew\SiteSettings\Traits\ManagesSettings;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model implements SettingInterface
{
    use ManagesSettings;

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
}
