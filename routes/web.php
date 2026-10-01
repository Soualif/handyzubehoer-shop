<?php

use App\Http\Middleware\SetLocale;
use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return redirect()->route('home', ['locale' => Locale::detect($request)]);
});

Route::prefix('{locale}')
    ->whereIn('locale', Locale::supported())
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::view('/', 'home')->name('home');
    });
