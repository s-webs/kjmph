<?php

use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Auth\ForgotController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\MoonShine\Pages\ResetPasswordPage;
use App\Services\Antiplagiat\AntiplagiatClient;
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
    Route::get('/collections', [\App\Http\Controllers\Public\CollectionsController::class, 'index'])->name('collections.index');

    Route::controller(AuthenticateController::class)->group(function () {
        Route::get('/login', 'form')->middleware('guest')->name('login');
        Route::post('/login', 'authenticate')->middleware('guest')->name('authenticate');
        Route::delete('/logout', 'logout')->middleware('auth')->name('logout');
    });

    Route::group([
        'middleware' => ['auth'],
        'prefix' => 'author'
    ], function () {
        Route::get('profile', [\App\Http\Controllers\Author\ProfileController::class, 'edit'])->name('author.profile.edit');
        Route::get('articles', [\App\Http\Controllers\Author\ArticlesController::class, 'index'])->name('author.articles.index');
        Route::get('article/create', [\App\Http\Controllers\Author\ArticlesController::class, 'create'])->name('author.article.create');
        Route::post('article/store', [\App\Http\Controllers\Author\ArticlesController::class, 'store'])->name('author.article.store');
        Route::get('article/{article}/details', [\App\Http\Controllers\Author\ArticlesController::class, 'details'])->name('author.article.details');
    });
});

Route::controller(ForgotController::class)->middleware('guest')->group(function () {
    Route::get('/forgot', 'form')->name('forgot');
    Route::post('/forgot', 'reset');
    Route::get('/reset-password/{token}', static fn(ResetPasswordPage $page) => $page)->name('password.reset');
    Route::post('/reset-password', 'updatePassword')->name('password.update');
});

Route::controller(RegisterController::class)->middleware('guest')->group(function () {
    Route::get('/register', 'form')->name('register');
    Route::post('/register', 'store')->name('register.store');
});

Route::controller(ProfileController::class)->middleware('auth')->prefix('profile')->group(function () {
    Route::get('/', 'index')->name('profile');
    Route::post('/', 'update')->name('profile.update');
});


Route::get('mail-test', function () {
    Mail::raw('Это тестовое письмо из Laravel.', function ($message) {
        $message->to('swebs.sh@gmail.com') // сюда свой e-mail
        ->subject('Тест отправки письма');
    });

    return 'Тестовое письмо отправлено (если почта настроена).';
});

Route::get('/antiplagiat-test', function () {
    $config = config('antiplagiat');
    $service = new AntiplagiatClient(
        $config['login'],
        $config['password'],
        $config['company_name'],
        $config['apicorp_address'],
    );

    $client = $service->getClient();

    // дальше будем вызывать методы API...
    dd($client);
});
