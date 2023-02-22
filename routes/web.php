<?php

use App\Http\Controllers\{
    ProductController,
    CheckoutController
};

use Illuminate\Support\Facades\Route;

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


Route::get('/', [ProductController::class, 'index'])->name("products.index");
Route::post('checkout', [CheckoutController::class, 'checkout'])->name("checkout");
Route::get('checkout/success', [CheckoutController::class, 'success'])->name("checkout.success");
Route::get('checkout/cancel', [CheckoutController::class, 'cancel'])->name("checkout.cancel");
Route::post('checkout/webhook', [CheckoutController::class, 'webhook'])->name("checkout.webhook");
