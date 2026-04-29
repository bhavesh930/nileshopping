<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JWTAuthController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\CategoryAuthController;
use App\Http\Controllers\Api\ListingAuthController;

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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::group([
    'middleware' => 'api',
    //'prefix' => 'auth'
], function ($router) {
    Route::post('/signup', [JwtAuthController::class, 'register']);
    Route::post('/signin', [JwtAuthController::class, 'login']);
    Route::get('/user', [JwtAuthController::class, 'user']);
    Route::post('/token-refresh', [JwtAuthController::class, 'refresh']);
    Route::post('/signout', [JwtAuthController::class, 'signout']);
    Route::post('/login', [JwtAuthController::class, 'mobileLogin']);
    //User Controller
    Route::get('/user/address', [UserAuthController::class, 'userAddress']);
    Route::post('/user/add/address', [UserAuthController::class, 'addAddress']);
    Route::post('/user/address/default', [UserAuthController::class, 'makeDefaultAddress']);
    Route::post('/user/address/edit', [UserAuthController::class, 'editAddress']);
    Route::post('/user/profile/edit', [UserAuthController::class, 'profileEdit']);
    //Category Controller
    Route::get('/category/list', [CategoryAuthController::class, 'getCategory']);
    //Product 
    Route::get('/category/product/list', [ListingAuthController::class, 'getCategoryListing']);
    Route::get('/product/detail', [ListingAuthController::class, 'productDetail']);
    //Whishlist
    Route::post('/add/product/whishlist', [ListingAuthController::class, 'addProductWhishList']);
    Route::get('/whishlist/list', [ListingAuthController::class, 'whishList']);
    //Cart
    Route::post('/add/cart', [ListingAuthController::class, 'addToCart']);
    Route::get('/cart/list', [ListingAuthController::class, 'cartProducts']);
    Route::get('/cart/delete', [ListingAuthController::class, 'removeProductFromCart']);
    Route::post('/checkout', [ListingAuthController::class, 'checkOutList']);
    Route::post('/order', [ListingAuthController::class, 'orderingProcess']);
    Route::get('/order/history', [ListingAuthController::class, 'orderHistory']);
    Route::post('/order/cancel', [ListingAuthController::class, 'orderCancel']);
    //review
    Route::post('/order/review', [ListingAuthController::class, 'orderReview']);

    Route::get('/home/screen', [ListingAuthController::class, 'productFilterListing']);

    // Route::get('/best/selling', [ListingAuthController::class, 'productFilterListing'])->defaults('slug', '1');
    // Route::get('/trending/now', [ListingAuthController::class, 'productFilterListing'])->defaults('slug', '2');
    // Route::get('/new/noteworthy', [ListingAuthController::class, 'productFilterListing'])->defaults('slug', '3');
    // Route::get('/accessories', [ListingAuthController::class, 'productFilterListing'])->defaults('slug', '4');
    // Route::get('/recently/viewed', [ListingAuthController::class, 'productFilterListing'])->defaults('slug', '5');
    // Route::get('/shop/now', [ListingAuthController::class, 'productFilterListing'])->defaults('slug', '6');
    // Route::get('/quick/buy', [ListingAuthController::class, 'productFilterListing'])->defaults('slug', '7');
});