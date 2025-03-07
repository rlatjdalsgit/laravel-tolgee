<?php

namespace Snippet\LaravelTolgee\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Snippet\LaravelTolgee\Console\Commands\DeleteAllKeysCommand;
use Snippet\LaravelTolgee\Console\Commands\ImportKeysCommand;  // 🚀 Artisan 명령어 추가
use Snippet\LaravelTolgee\Console\Commands\SyncTranslationsCommand;
use Snippet\LaravelTolgee\Services\TolgeeService;

class TolgeeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 🚀 Artisan 명령어 등록
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportKeysCommand::class,
                SyncTranslationsCommand::class,
                DeleteAllKeysCommand::class,
            ]);
        }

        // 🚀 설정 파일 퍼블리싱
        $this->publishes([
            __DIR__ . '/../../config/tolgee.php' => config_path('tolgee.php'),
        ], 'tolgee-config');
    }
}
