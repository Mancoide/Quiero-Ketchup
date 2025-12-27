<?php

namespace App\Observers;

use App\Models\Category;
use App\Support\ApiCacheVersion;

class CategoryObserver
{
    public function created(Category $category): void
    {
        ApiCacheVersion::bump('categories');
    }

    public function updated(Category $category): void
    {
        ApiCacheVersion::bump('categories');
    }

    public function deleted(Category $category): void
    {
        ApiCacheVersion::bump('categories');
    }

    public function restored(Category $category): void
    {
        ApiCacheVersion::bump('categories');
    }

    public function forceDeleted(Category $category): void
    {
        ApiCacheVersion::bump('categories');
    }
}
