<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Restaurants\RestaurantsController;
use App\Http\Controllers\Restaurants\RestaurantPhotosController;
use App\Http\Controllers\RaiseOrders\RaiseOrdersController;
use App\Http\Controllers\Orders\OrdersController;
use App\Http\Controllers\TradingRecords\TradingRecordsController;
use App\Http\Controllers\RestaurantComments\RestaurantCommentsController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(Auth::check()) {
      return Inertia::render('RaiseOrders/RaiseOrdersList');
    } else {
      return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
      ]);
    }

});

// dashboard 暫不需要

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/trading-record', function () {
    return Inertia::render('TradingRecord/TradingRecord');
})->name('trading-record');

Route::middleware(['auth:sanctum', 'verified'])->get('/restaurant-list', function () {
  return Inertia::render('RestaurantList/RestaurantList');
})->name('restaurant-list');

Route::middleware(['auth:sanctum', 'verified'])->get('/raise-orders', function () {
  return Inertia::render('RaiseOrders/RaiseOrdersList');
})->name('raise-orders');

// restaurant api
Route::middleware(['auth:sanctum', 'verified'])->resource('restaurant-api', RestaurantsController::class, ['except' => [
  'create'
]]);

// restaurant photos api
Route::middleware(['auth:sanctum', 'verified'])->resource('restaurant-photos-api', RestaurantPhotosController::class, ['only' => [
  'show', 'store', 'destroy'
]]);

// restaurant comments api
Route::middleware(['auth:sanctum', 'verified'])->resource('restaurant-comment-api', RestaurantCommentsController::class, ['only' => [
  'edit', 'store', 'update', 'destroy'
]]);

// raise orders api
Route::middleware(['auth:sanctum', 'verified'])->resource('raise-orders-api', RaiseOrdersController::class, ['only' => [
  'index', 'edit', 'store', 'update', 'destroy'
]]);

// raise orders api
Route::middleware(['auth:sanctum', 'verified'])->prefix('raise-orders-api')->group(function(){
  Route::put('/{id}/completed', [RaiseOrdersController::class, 'completed'])->name('raise-orders-api.completed');
});

// orders api
Route::middleware(['auth:sanctum', 'verified'])->resource('orders-api', OrdersController::class, ['only' => [
  'index', 'show', 'edit', 'store', 'update', 'destroy'
]]);

// orders api sum
Route::middleware(['auth:sanctum', 'verified'])->prefix('orders-api')->group(function(){
  Route::get('/{id}/sum', [OrdersController::class, 'sum'])->name('orders-api.sum');
});

// trading-record-api
Route::middleware(['auth:sanctum', 'verified'])->resource('trading-record-api', TradingRecordsController::class, ['only' => [
  'index', 'show', 'edit'
]]);

// trading-record-api
Route::middleware(['auth:sanctum', 'verified'])->resource('trading-record-api', TradingRecordsController::class, ['only' => [
  'destroy', 'update'
]]);

// trading-record-api
Route::middleware(['auth:sanctum', 'verified'])->prefix('trading-record-api')->group(function(){
  Route::get('/chat/show', [TradingRecordsController::class, 'chat'])->name('trading-record.chat');
  Route::get('/chat/monthlyCost', [TradingRecordsController::class, 'monthly_cost'])->name('trading-record-api.monthlyCost');
});
