<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
        'web',
    ],
], function () {
    Route::get('/', function () {
        return view('pages.home.index');
    });
});
