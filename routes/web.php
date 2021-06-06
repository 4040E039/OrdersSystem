<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Jetstream\Http\Controllers\TeamInvitationController;
use App\Http\Controllers\Restaurants\RestaurantsController;
use App\Http\Controllers\Restaurants\RestaurantPhotosController;
use App\Http\Controllers\RaiseOrders\RaiseOrdersController;
use App\Http\Controllers\Orders\OrdersController;
use App\Http\Controllers\TradingRecords\TradingRecordsController;
use App\Http\Controllers\RestaurantComments\RestaurantCommentsController;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
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

// restaurant page
Route::middleware(['auth:sanctum', 'verified'])->prefix('restaurant-list')->group(function(){
  Route::get('/{id}', [RestaurantsController::class, 'show'])->name('restaurant-list.detail');
  Route::get('/edit/{id}', [RestaurantsController::class, 'edit'])->name('restaurant-list.edit');
  Route::get('/photos/{id}', [RestaurantPhotosController::class, 'show'])->name('restaurant-list.photos');
});

// restaurant api
Route::middleware(['auth:sanctum', 'verified'])->prefix('restaurant-api')->group(function(){
  Route::post('/', [RestaurantsController::class, 'index'])->name('restaurant-api.index');
  Route::post('/store', [RestaurantsController::class, 'store'])->name('restaurant-api.store');
  Route::put('/{id}', [RestaurantsController::class, 'update'])->name('restaurant-api.update');
  Route::delete('/{id}', [RestaurantsController::class, 'destroy'])->name('restaurant-api.destroy');
});

// restaurant comments api
Route::middleware(['auth:sanctum', 'verified'])->prefix('restaurant-comment-api')->group(function(){
  Route::get('/{id}', [RestaurantCommentsController::class, 'edit'])->name('restaurant-comment-api.edit');
  Route::post('/store', [RestaurantCommentsController::class, 'store'])->name('restaurant-comment-api.store');
  Route::put('/{id}', [RestaurantCommentsController::class, 'update'])->name('restaurant-comment-api.update');
  Route::delete('/{id}', [RestaurantCommentsController::class, 'destroy'])->name('restaurant-comment-api.destroy');
});

// restaurant photos api
Route::middleware(['auth:sanctum', 'verified'])->prefix('restaurant-photos-api')->group(function(){
  Route::post('/store', [RestaurantPhotosController::class, 'store'])->name('restaurant-photos-api.store');
  Route::delete('/{id}', [RestaurantPhotosController::class, 'destroy'])->name('restaurant-photos-api.destroy');
});

// raise orders api
Route::middleware(['auth:sanctum', 'verified'])->prefix('raise-orders-api')->group(function(){
  Route::post('/', [RaiseOrdersController::class, 'index'])->name('raise-orders-api.index');
  Route::get('/{id}', [RaiseOrdersController::class, 'edit'])->name('raise-orders-api.edit');
  Route::post('/store', [RaiseOrdersController::class, 'store'])->name('raise-orders-api.store');
  Route::put('/{id}', [RaiseOrdersController::class, 'update'])->name('raise-orders-api.update');
  Route::delete('/{id}', [RaiseOrdersController::class, 'destroy'])->name('raise-orders-api.destroy');
  Route::put('/completed/{id}', [RaiseOrdersController::class, 'completed'])->name('raise-orders-api.completed');
});

// orders page
Route::middleware(['auth:sanctum', 'verified'])->prefix('orders')->group(function(){
  Route::get('/{id}', [OrdersController::class, 'index'])->name('orders.index');
});

// orders api
Route::middleware(['auth:sanctum', 'verified'])->prefix('orders-api')->group(function(){
  Route::post('/', [OrdersController::class, 'show'])->name('orders-api.show');
  Route::get('/{id}', [OrdersController::class, 'edit'])->name('orders-api.edit');
  Route::post('/store', [OrdersController::class, 'store'])->name('orders-api.store');
  Route::delete('/{id}', [OrdersController::class, 'destroy'])->name('orders-api.destroy');
  Route::put('/{id}', [OrdersController::class, 'update'])->name('orders-api.update');
});

// trading-record-list
Route::middleware(['auth:sanctum', 'verified'])->prefix('trading-record-list')->group(function(){
  Route::get('/{id}', [TradingRecordsController::class, 'show'])->name('trading-record-list.detail');
  Route::get('/edit/{id}', [TradingRecordsController::class, 'edit'])->name('trading-record-list.edit');
  Route::get('/chat/show', [TradingRecordsController::class, 'chat'])->name('trading-record.chat');
});


// trading-record-api
Route::middleware(['auth:sanctum', 'verified'])->prefix('trading-record-api')->group(function(){
  Route::post('/', [TradingRecordsController::class, 'index'])->name('trading-record-api.index');
  Route::delete('/{id}', [TradingRecordsController::class, 'destroy'])->name('trading-record-api.destroy');
  Route::put('/{id}', [TradingRecordsController::class, 'update'])->name('restaurant-api.update');
  Route::get('/chat/monthlyCost', [TradingRecordsController::class, 'monthly_cost'])->name('trading-record-api.monthlyCost');
});
