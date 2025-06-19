<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/products/create', [ProductController::class, 'create']);
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::post('/get-variant', [ProductController::class, 'getVariant'])->name('products.getVariant');


Route::post('/add-to-cart', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', function () {
    // session()->flush(); // Clear session flash data
    return session('cart', []);
});
