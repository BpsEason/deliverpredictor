<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 如果存在維護模式檔案，載入它
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// 載入 Composer autoload
require __DIR__.'/../vendor/autoload.php';

// 建立 Laravel 應用程式實例
(require_once __DIR__.'/../bootstrap/app.php')
    ->make(Illuminate\Contracts\Http\Kernel::class)
    ->handle(Request::capture())
    ->send();
