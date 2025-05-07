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

        // Get the latest records
        $latestProduct = Product::latest()->first();  // Fetch the most recent product
        $latestCategory = Category::latest()->first();  // Fetch the most recent category
        $latestTag = Tag::latest()->first();  // Fetch the most recent tag
        $latestCustomer = Customer::latest()->first();  // Fetch the most recent customer

        // Pass data to the view
        return view("admin.dashboard", compact(
            'productCount',
            'tagCount',
            'categoryCount',
            'customerCount',
            'latestProduct',   // Ensure this variable is passed to the view
            'latestCategory',  // Ensure this variable is passed to the view
            'latestTag',       // Ensure this variable is passed to the view
            'latestCustomer'   // Ensure this variable is passed to the view
        ));
    }
}
