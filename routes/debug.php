<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/debug/session', function () {
    return [
        'session_driver' => config('session.driver'),
        'session_domain' => config('session.domain'),
        'session_secure' => config('session.secure'),
        'session_http_only' => config('session.http_only'),
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
        'app_url' => config('app.url'),
    ];
});
