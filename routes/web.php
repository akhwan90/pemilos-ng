<?php

use Illuminate\Support\Facades\Route;

// Route fallback untuk SPA Vue
Route::get('/', function () {
    return view('app');
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|storage).*$');
