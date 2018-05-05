<?php

namespace BWibrew\SiteSettings\Traits;

use BWibrew\SiteSettings\Interfaces\SettingInterface;

trait ManagesSettingScopes
{
    /**
     * Get the settings for this scope.
     */
    public function settings()
    {
        return $this->hasMany(app(SettingInterface::class));
    }
}
