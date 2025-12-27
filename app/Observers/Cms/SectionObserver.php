<?php

namespace App\Observers\Cms;

use App\Models\Section;
use App\Support\ApiCacheVersion;

class SectionObserver
{
    public function created(Section $section): void
    {
        ApiCacheVersion::bump('cms_sections');
    }

    public function updated(Section $section): void
    {
        ApiCacheVersion::bump('cms_sections');
    }

    public function deleted(Section $section): void
    {
        ApiCacheVersion::bump('cms_sections');
    }

    public function restored(Section $section): void
    {
        ApiCacheVersion::bump('cms_sections');
    }

    public function forceDeleted(Section $section): void
    {
        ApiCacheVersion::bump('cms_sections');
    }
}
