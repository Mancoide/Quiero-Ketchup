<?php

namespace App\Observers\Cms;

use App\Models\LegalText;
use App\Support\ApiCacheVersion;

class LegalTextObserver
{
    public function created(LegalText $legalText): void
    {
        ApiCacheVersion::bump('cms_legal_texts');
    }

    public function updated(LegalText $legalText): void
    {
        ApiCacheVersion::bump('cms_legal_texts');
    }

    public function deleted(LegalText $legalText): void
    {
        ApiCacheVersion::bump('cms_legal_texts');
    }

    public function restored(LegalText $legalText): void
    {
        ApiCacheVersion::bump('cms_legal_texts');
    }

    public function forceDeleted(LegalText $legalText): void
    {
        ApiCacheVersion::bump('cms_legal_texts');
    }
}
