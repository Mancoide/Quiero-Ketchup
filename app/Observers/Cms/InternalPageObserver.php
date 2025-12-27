<?php

namespace App\Observers\Cms;

use App\Models\InternalPage;
use App\Support\ApiCacheVersion;

class InternalPageObserver
{
    public function created(InternalPage $internalPage): void
    {
        ApiCacheVersion::bump('cms_internal_pages');
        ApiCacheVersion::bump('cms_sections');
    }

    public function updated(InternalPage $internalPage): void
    {
        ApiCacheVersion::bump('cms_internal_pages');
        ApiCacheVersion::bump('cms_sections');
    }

    public function deleted(InternalPage $internalPage): void
    {
        ApiCacheVersion::bump('cms_internal_pages');
        ApiCacheVersion::bump('cms_sections');
    }

    public function restored(InternalPage $internalPage): void
    {
        ApiCacheVersion::bump('cms_internal_pages');
        ApiCacheVersion::bump('cms_sections');
    }

    public function forceDeleted(InternalPage $internalPage): void
    {
        ApiCacheVersion::bump('cms_internal_pages');
        ApiCacheVersion::bump('cms_sections');
    }
}
