<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboradController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::controller(DashboradController::class)->group(function () {

    Route::get('home', 'home');
    Route::get('products-by-brand/{brand}', 'products_by_brand');
    Route::get('products-view/{id}', 'products_view');
    Route::get('filters/{filters}', 'filters');
    Route::get('filters/{brand}/{filters}', 'filters_by_brand');
    Route::get('add-favorite/{product_id}','add_favorite')->middleware('auth:sanctum');
    Route::get('fetch-favorite','fetch_favorite')->middleware('auth:sanctum');
    Route::get('favorite-remove/{id}','remove_favorite')->middleware('auth:sanctum');
    Route::any('add-cart','add_cart')->middleware('auth:sanctum');
    Route::get('cart-fetch','cart_fetch')->middleware('auth:sanctum');
    Route::get('cart-remove/{id}','cart_remove');
    Route::get('cart-remove-all','cart_remove_all');
});

Route::controller(AuthController::class)->group(function () {
    Route::any('login','login');
    Route::post('register','register');
});
