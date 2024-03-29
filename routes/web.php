<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ThumbnailController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::get('/storage/images/{dir}/{method}/{size}/{file}', ThumbnailController::class)
        ->where('method', 'resize|crop|fit')
        ->where('size', '\d+x\d+')
        ->where('file', '.*\.(png|jpg|jpeg)$')
        ->name('thumbnail');

    Route::controller(SignInController::class)->group(function () {
        Route::get('/login', 'page')->name('login');
        Route::post('/login', 'handle')->middleware('throttle:auth')->name('login.handle');
        Route::delete('/logout', 'logout')->name('logout');
    });

    Route::controller(SignUpController::class)->group(function () {
        Route::get('/signup', 'page')->name('register');
        Route::post('/signup', 'handle')->middleware('throttle:auth')->name('register.handle');
    });

    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('/forgot-password', 'page')->middleware('guest')->name('forgot');
        Route::post('/forgot-password', 'handle')->middleware('guest')->name('forgot.handle');
    });

    Route::controller(ResetPasswordController::class)->group(function () {
        Route::get('/reset-password/{token}', 'page')->middleware('guest')->name('password.reset');
        Route::post('/reset-password', 'handle')->middleware('guest')->name('password-reset.handle');
    });

    Route::controller(SocialAuthController::class)->group(function () {
        Route::get('/auth/socialite/{driver}', 'redirect')->name('socialite.redirect');

        Route::get('/auth/socialite/{driver}/callback', 'callback')->name('socialite.callback');
    });
});
