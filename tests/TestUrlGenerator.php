<?php

namespace BWibrew\SiteSettings\Tests;

use Spatie\MediaLibrary\UrlGenerator\BaseUrlGenerator;

class TestUrlGenerator extends BaseUrlGenerator
{
    /**
     * Get the URL for the profile of a media item.
     *
     * @return string
     */
    public function getUrl() : string
    {
        return realpath(public_path('media').'/'.$this->getPathRelativeToRoot());
    }

    /*
     * Get the path for the profile of a media item.
     */
    public function getPath(): string
    {
        return realpath(public_path('media')).'/'.$this->getPathRelativeToRoot();
    }
}
