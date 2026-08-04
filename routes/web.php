<?php

use Illuminate\Support\Facades\Route;
use Intervention\Image\ImageManager;
// Explicit route for mews/captcha to avoid API middleware interference
Route::get('/captcha/api/{config?}', '\Mews\Captcha\CaptchaController@getCaptchaApi')->middleware('web');

// Route fallback untuk SPA Vue
Route::get('/', function () {
    return view('app');
});

// Route::get('/{any}', function () {
//     return view('app');
// })->where('any', '^(?!api|storage|captcha).*$');
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!(api|storage|captcha)).*$(?<!\.(png|jpg|jpeg|gif|svg|ico|css|js|webp))');
