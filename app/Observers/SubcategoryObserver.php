<?php

namespace App\Observers;

use App\Models\Subcategory;
use App\Support\ApiCacheVersion;

class SubcategoryObserver
{
    public function created(Subcategory $subcategory): void
    {
        ApiCacheVersion::bump('categories');
    }

    public function updated(Subcategory $subcategory): void
    {
        ApiCacheVersion::bump('categories');
    }

    public function deleted(Subcategory $subcategory): void
    {
        ApiCacheVersion::bump('categories');
    }

    public function restored(Subcategory $subcategory): void
    {
        ApiCacheVersion::bump('categories');
    }

    public function forceDeleted(Subcategory $subcategory): void
    {
        ApiCacheVersion::bump('categories');
    }
}
