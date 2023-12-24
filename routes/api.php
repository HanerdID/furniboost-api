<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/login', [LoginController::class, 'login'])->name('api.login');
Route::post('/confirm-password', [ConfirmPasswordController::class, 'confirm'])->name('api.confirm-password');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('api.forgot-password');
Route::post('/register', [RegisterController::class, 'register'])->name('api.register');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('api.reset-password');
Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])->name('api.verify-email');
Route::get('/email/resend', [VerificationController::class, 'resend'])->name('api.resend-verification');

// Routes for authenticated users
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show_profile'])->name('api.profile.show');
    Route::post('/profile/{id}', [ProfileController::class, 'edit_profile'])->name('api.profile.edit');

    // Routes for admin
    Route::post('/product', [ProductController::class, 'store_product'])->name('store_product');
    Route::post('/product/update/{product}', [ProductController::class, 'update_product'])->name('update_product');
    Route::delete('/product/delete/{product}', [ProductController::class, 'delete_product'])->name('api.product.delete');

    Route::post('/order/{order}/confirm', [OrderController::class, 'confirm_payment'])->name('api.order.confirm_payment');

    // Common routes for all authenticated users
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('api.order.checkout');
    Route::get('/order', [OrderController::class, 'index_order'])->name('api.order.index');
    Route::get('/order/{order}', [OrderController::class, 'show_order'])->name('api.order.show');
    Route::post('/order/{order}/pay', [OrderController::class, 'submit_payment_receipt'])->name('api.order.submit_payment_receipt');


    Route::post('/cart/add/{product}', [CartController::class, 'add_to_cart'])->name('api.cart.add_to_cart');
    Route::get('/cart/show', [CartController::class, 'show_cart'])->name('api.cart.show_cart');
    Route::post('/cart/update/{cart}', [CartController::class, 'update_cart'])->name('api.cart.update_cart');
    Route::delete('/cart/delete/{cart}', [CartController::class, 'delete_cart'])->name('api.cart.delete_cart');
});
