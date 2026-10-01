<?php

use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\PageController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Middleware\SetLocale;
use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return redirect()->route('home', ['locale' => Locale::detect($request)]);
});

Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::prefix('{locale}')
    ->whereIn('locale', Locale::supported())
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('/', [CatalogController::class, 'home'])->name('home');
        Route::get('/products', [CatalogController::class, 'index'])->name('products.index');
        Route::get('/category/{category}', [CatalogController::class, 'index'])->name('categories.show');
        Route::get('/product/{product}', [CatalogController::class, 'show'])->name('products.show');

        Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
        Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{variant}', [CartController::class, 'update'])->name('cart.update');

        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout');
        Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

        Route::get('/info/{page}', [PageController::class, 'show'])
            ->whereIn('page', PageController::PAGES)
            ->name('page');
    });
