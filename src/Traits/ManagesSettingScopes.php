<?php

namespace BWibrew\SiteSettings\Traits;

trait ManagesSettingScopes
{
    /**
     * Get the settings for this scope.
     */
    public function settings()
    {
        return $this->hasMany(Setting::class);
    }
}
