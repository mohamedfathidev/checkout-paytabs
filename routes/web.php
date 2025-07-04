<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;

// main page of creating order 
Route::get('/', function () {
    return view('order');
})->name('home');

// create the roder
Route::post('orders/create', [OrderController::class, 'store'])->name('order.create');

// checkout the order
Route::get('/checkout/{id}', [CheckoutController::class, 'showCheckoutDetailsForm'])->name('show.checkout-details.form')->middleware('ensure.orde.not.Paid');
Route::post('/checkout/{order}', [CheckoutController::class, 'checkout'])->name('checkout');



// After Payment proccessing routes 
Route::post('/payment/return', [PaymentController::class, 'handleReturnUrl'])->name('payment.return');


// if the server use Http paytabs server make GET request with empty playload unlike HTTPS 
Route::middleware('signed')->group(function () {
    Route::match(['get', 'post'], '/payment/success/{id?}', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::match(['get', 'post'], '/payment/failure', [PaymentController::class, 'paymentFailure'])->name('payment.failure');
});

Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');

// refund the order 
Route::get('payment/refund/{id}', [PaymentController::class, 'fullRefund'])->name('payment.refund');


// error redirection page 
Route::get('/error', function () {

    return view('error');
})->name('error');
