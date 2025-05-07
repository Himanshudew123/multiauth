<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Customer;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Count records
        $productCount = Product::count();
        $tagCount = Tag::count();
        $categoryCount = Category::count();
        $customerCount = Customer::count();
    
        // Get the latest 5 records
        $latestProducts = Product::latest()->take(5)->get();     // or ->limit(5)
        $latestCategories = Category::latest()->take(5)->get();
        $latestTags = Tag::latest()->take(5)->get();
        $latestCustomers = Customer::latest()->take(5)->get();
    
        // Pass data to the view
        return view("admin.dashboard", compact(
            'productCount',
            'tagCount',
            'categoryCount',
            'customerCount',
            'latestProducts',
            'latestCategories',
            'latestTags',
            'latestCustomers'
        ));
    }
    
}
