<?php

namespace BWibrew\SiteSettings\Interfaces;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;
use Spatie\MediaLibrary\FileAdder\FileAdder;

interface SettingWithMedia extends Setting, HasMedia
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

    /**
     * Ensure compatibility with multiple versions of Spatie Media Library.
     *
     * @param FileAdder $fileAdder
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function addFileToMediaCollection(FileAdder $fileAdder);
}
