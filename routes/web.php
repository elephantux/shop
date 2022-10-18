<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    logger()->channel('telegram')->debug('hey!');
    return view('welcome');
});
