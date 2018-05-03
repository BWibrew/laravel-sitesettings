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
        'updated_by',
    ];

    protected $table = 'settings';

    /**
     * Get the scope that owns the setting.
     */
    public function scope()
    {
        return $this->belongsTo(Scope::class);
    }
}
