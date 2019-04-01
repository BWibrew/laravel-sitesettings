<?php

namespace BWibrew\SiteSettings\Interfaces;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia\HasMedia;

interface SettingWithMediaInterface extends SettingInterface, HasMedia
{
    /**
     * Syncs associated media library item.
     *
     * @param string $name
     * @param \Illuminate\Http\UploadedFile $value
     *
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function syncWithMediaLibrary(string $name, UploadedFile $value);
}
