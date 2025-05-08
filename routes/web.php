<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TagsController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Full CRUD routes for customers
    Route::post('/customers', [CustomerController::class,'index'])->name('customers.search');
    Route::get('/customers', [CustomerController::class,'index'])->name('customers.index');
    Route::put('/customers/{customer}', [CustomerController::class,'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class,'destroy'])->name('customers.destroy');
    Route::get('/customers/create', [CustomerController::class,'create'])->name('customers.create');
    Route::post('/customers/store', [CustomerController::class,'store'])->name('customers.store');
    Route::get('/customers/export/csv', [CustomerController::class, 'exportCSV'])->name('customers.export.csv');
    Route::get('/customers/pdf', [CustomerController::class, 'exportPdfView'])->name('customers.pdf');
    Route::get('/customers/{customer}', [CustomerController::class,'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class,'edit'])->name('customers.edit');
    




    Route::post('/categories', [CategoriesController::class, 'index'])->name('categories.search');
    Route::get('/categories', [CategoriesController::class, 'index'])->name('categories.index');
    Route::put('/categories/{categories}', [CategoriesController::class,'update'])->name('categories.update');
    Route::delete('/categories/{categories}', [CategoriesController::class,'destroy'])->name('categories.destroy');
    Route::get('/categories/create', [CategoriesController::class,'create'])->name('categories.create');
    Route::post('/categories/store', [CategoriesController::class,'store'])->name('categories.store');
    Route::get('/categories/export/csv', [CategoriesController::class, 'exportCSV'])->name('categories.export.csv');
    Route::get('/categories/pdf', [CategoriesController::class, 'exportPdfView'])->name('categories.pdf');
    Route::get('/categories/{categories}/edit', [CategoriesController::class,'edit'])->name('categories.edit');
    


    
    
    Route::post('/tags', [TagsController::class, 'index'])->name('tags.search');
    Route::get('/tags', [TagsController::class, 'index'])->name('tags.index');
    Route::put('/tags/{tags}', [TagsController::class,'update'])->name('tags.update');
    Route::delete('/tags/{tags}', [TagsController::class,'destroy'])->name('tags.destroy');
    Route::get('/tags/create', [TagsController::class,'create'])->name('tags.create');
    Route::post('/tags/store', [TagsController::class,'store'])->name('tags.store');
    Route::get('/tags/pdf', [TagsController::class, 'exportPdfView'])->name('tags.pdf');
    Route::get('/tags/export/csv', action: [TagsController::class, 'exportCSV'])->name('tags.export.csv');
    Route::get('/tags/{tags}/edit', [TagsController::class,'edit'])->name('tags.edit');
   



    Route::post('/products', [ProductController::class, 'index'])->name('products.search');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::put('/products/{products}', [ProductController::class,'update'])->name('products.update');
    Route::delete('/products/{products}', [ProductController::class,'destroy'])->name('products.destroy');
    Route::get('/products/create', [ProductController::class,'create'])->name('products.create');
    Route::post('/products/store', [ProductController::class,'store'])->name('products.store');
    Route::get('/products/export/csv', [ProductController::class, 'exportCSV'])->name('products.export.csv');
    Route::get('/products/pdf', [ProductController::class, 'exportPdfView'])->name('products.pdf');
    Route::get('/products/{products}', [ProductController::class,'show'])->name('products.show');
    Route::get('/products/{products}/edit', [ProductController::class,'edit'])->name('products.edit');
   
    
});
