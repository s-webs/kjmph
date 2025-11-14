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
    Route::get('/', [\App\Http\Controllers\Public\HomeController::class, 'index'])->name('home');
    Route::get('/pages/{page}', [\App\Http\Controllers\Public\PagesController::class, 'show'])->name('pages.show');
    Route::get('/release', [\App\Http\Controllers\Public\JournalsController::class, 'release'])->name('journal.release');
    Route::get('/archive', [\App\Http\Controllers\Public\JournalsController::class, 'archive'])->name('journal.archive');
});
