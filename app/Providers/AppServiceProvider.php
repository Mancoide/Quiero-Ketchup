<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\InternalPage;
use App\Models\LegalText;
use App\Models\Order;
use App\Models\Section;
use App\Models\Subcategory;
use App\Observers\CategoryObserver;
use App\Observers\Cms\BannerObserver;
use App\Observers\Cms\InternalPageObserver;
use App\Observers\Cms\LegalTextObserver;
use App\Observers\Cms\SectionObserver;
use App\Observers\OrderObserver;
use App\Observers\SubcategoryObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Category::observe(CategoryObserver::class);
        Subcategory::observe(SubcategoryObserver::class);

        Order::observe(OrderObserver::class);

        Section::observe(SectionObserver::class);
        Banner::observe(BannerObserver::class);
        InternalPage::observe(InternalPageObserver::class);
        LegalText::observe(LegalTextObserver::class);
    }
}
