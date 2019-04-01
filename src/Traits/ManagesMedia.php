<?php

namespace BWibrew\SiteSettings\Traits;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait as SpatieMedia;

trait ManagesMedia
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

        $this->addMedia($value)->usingName($name)->toMediaCollection();

        switch (config('sitesettings.file_value_type')) {
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
}
