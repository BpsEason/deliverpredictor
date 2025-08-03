#!/bin/sh

# 複製環境設定範本
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# 安裝 Laravel 依賴
composer install --no-dev --optimize-autoloader

# 自動生成 Laravel APP_KEY
php artisan key:generate --ansi

# 執行資料庫遷移
php artisan migrate --force

# 啟動 Supervisor 來管理 PHP-FPM
/usr/bin/supervisord -c /etc/supervisord/supervisord.conf
