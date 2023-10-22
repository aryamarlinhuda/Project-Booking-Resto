<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RestoController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function() {
    Route::post('register','register');
    Route::post('login','login');
    Route::post('refresh-token','refresh_token');
    Route::post('logout','logout');
});

Route::group(['middleware' => 'auth:api'], function() {
    Route::controller(RestoController::class)->group(function() {
        Route::get('list-category','list_category');
        Route::get('list-province','list_province');
        Route::get('list-city','list_city');
        Route::get('list-city-by-province/{id}','list_city_by_province');
        Route::prefix('resto')->group(function() {
            Route::get('list','list');
            Route::get('detail/{id}','detail');
            Route::get('filter-by-category/{id}','filter_by_category');
            Route::get('filter-by-province/{id}','filter_by_province');
            Route::get('filter-by-city/{id}','filter_by_city');
        });
    });
    Route::controller(CartController::class)->group(function() {
        Route::prefix('cart')->group(function() {
            Route::get('list','list');
            Route::post('add','add');
            Route::post('edit','edit');
            Route::post('remove','remove');
        });
    });
    Route::controller(OrderController::class)->group(function() {
        Route::prefix('order')->group(function() {
            Route::get('list','list');
            Route::get('detail/{id}','detail');
            Route::post('resto','order_resto');
            Route::post('resto-via-cart','order_via_cart');
        });
    });
    // Route::controller(ReviewController::class)->group(function() {
    //     Route::prefix('review')->group(function() {
    //         Route::get('list/{id}','list');
    //         Route::post('add','add');
    //         Route::post('edit','edit');
    //         Route::post('delete','delete');
    //     });
    // });
    Route::controller(UserController::class)->group(function() {
        Route::prefix('user')->group(function() {
            Route::get('profile','profile');
            Route::post('edit-profile','edit_profile');
            Route::post('edit-password','edit_password');
            Route::post('save-resto','save_resto');
            Route::get('saved-resto','saved_resto');        
        });
    });
});