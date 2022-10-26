<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

Route::get('/avatar/{hash}', function($hash) {
    $size = 100;
    $icon = new \Jdenticon\Identicon();
    $icon->setValue($hash);
    $icon->setSize($size);

    $style = new \Jdenticon\IdenticonStyle();
    $style->setBackgroundColor('#21232a');
    $style->setHues([142]);
    $style->setColorLightness([0.42, 0.62]);
    $style->setGrayscaleLightness([0.29, 1.00]);
    $style->setColorSaturation(0.51);
    $style->setGrayscaleSaturation(0.00);
    $icon->setStyle($style);

    $icon->displayImage('png');
    return response('')->header('Content-Type', 'image/png');
});

Route::get('/{url?}', function () {
    return view('layouts.app');
})->where('url', '[ \/\w\:.-]*');
