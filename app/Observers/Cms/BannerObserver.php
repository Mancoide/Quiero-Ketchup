<?php

namespace App\Observers\Cms;

use App\Models\Banner;
use App\Support\ApiCacheVersion;

class BannerObserver
{
    public function created(Banner $banner): void
    {
        ApiCacheVersion::bump('cms_banners');
        ApiCacheVersion::bump('cms_sections');
    }

    public function updated(Banner $banner): void
    {
        ApiCacheVersion::bump('cms_banners');
        ApiCacheVersion::bump('cms_sections');
    }

    public function deleted(Banner $banner): void
    {
        ApiCacheVersion::bump('cms_banners');
        ApiCacheVersion::bump('cms_sections');
    }

    public function restored(Banner $banner): void
    {
        ApiCacheVersion::bump('cms_banners');
        ApiCacheVersion::bump('cms_sections');
    }

    public function forceDeleted(Banner $banner): void
    {
        ApiCacheVersion::bump('cms_banners');
        ApiCacheVersion::bump('cms_sections');
    }
}
