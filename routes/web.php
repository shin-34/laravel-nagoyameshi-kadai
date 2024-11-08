<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\NotSubscribed;
use App\Http\Controllers\Admin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\CategoryController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//会員側
Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::resource('user', UserController::class)->only(['index', 'edit', 'update']);
});

Route::group(['middleware' => 'guest:admin'], function (){
    Route::resource('restaurants', RestaurantController::class)->only(['index', 'show']);
});

//subscription
Route::group(['middleware' => ['guest:admin', 'auth', 'verified', NotSubscribed::class]], function (){
    Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
    Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
});

Route::group(['middleware' => ['guest:admin', 'auth', 'verified', Subscribed::class]], function (){
    Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
    Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
    Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
});

Route::group(['middleware' => ['guest:admin', 'auth', 'verified']], function (){
    Route::resource('restaurants.reviews', ReviewController::class)->only(['index']);
});

Route::group(['middleware' => ['guest:admin', 'auth', 'verified', Subscribed::class]], function (){
    Route::resource('restaurants.reviews', ReviewController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
});

require __DIR__.'/auth.php';



//admin
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::resource('users', Admin\UserController::class)->only(['index', 'show']);
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::resource('restaurants', Admin\RestaurantController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::resource('company', Admin\CompanyController::class)->only(['index', 'edit', 'update']);
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::resource('terms', Admin\TermController::class)->only(['index', 'edit', 'update']);
});


