<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Customer;

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
    
public function boot()
{
    View::composer('*', function ($view) {
        $view->with([
            'productCount' => Product::count(),
            'tagCount' => Tag::count(),
            'categoryCount' => Category::count(),
            'customerCount' => Customer::count(),
        ]);
    });
}
}
