<?php

namespace BWibrew\SiteSettings\Traits;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\FileAdder\FileAdder;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait as SpatieMedia;

trait HasMedia
{
    use SpatieMedia;

    /**
     * Syncs associated media library item.
     *
     * @param string $name
     * @param \Illuminate\Http\UploadedFile $value
     *
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function syncWithMediaLibrary(string $name, UploadedFile $value)
    {
        if (count(self::find($this->id)->getMedia()) > 0) {
            self::find($this->id)->getMedia()->first()->delete();
        }

        $this->addFileToMediaCollection($this->addMedia($value)->usingName($name));

        switch (config('sitesettings.media_value_type')) {
            case 'path':
                $this->value = $this->getMedia()->first()->getPath();
                $this->save();
                break;
            case 'url':
                $this->value = $this->getMedia()->first()->getUrl();
                $this->save();
                break;
            case 'file_name':
                $this->value = $this->getMedia()->first()->file_name;
                $this->save();
                break;
        }
    }

    /**
     * Ensure compatibility with multiple versions of Spatie Media Library.
     *
     * @param \Spatie\MediaLibrary\FileAdder\FileAdder $fileAdder
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function addFileToMediaCollection(FileAdder $fileAdder)
    {
        if (method_exists($fileAdder, 'toMediaCollection')) {
            // spatie/laravel-medialibrary v6
            $fileAdder->toMediaCollection();
        } elseif (method_exists($fileAdder, 'toMediaLibrary')) {
            // spatie/laravel-medialibrary v5
            $fileAdder->toMediaLibrary();
        }
    }
}
