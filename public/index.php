<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

try {
    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    $autoload = __DIR__.'/../vendor/autoload.php';
    $bootstrap = __DIR__.'/../bootstrap/app.php';

    if (! file_exists($autoload) || ! file_exists($bootstrap)) {
        throw new RuntimeException('Laravel bootstrap files are missing.');
    }

    require $autoload;

    /** @var Application $app */
    $app = require_once $bootstrap;
    $app->handleRequest(Request::capture());
} catch (Throwable $exception) {
    http_response_code(200);
    require __DIR__.'/standalone.php';
}
