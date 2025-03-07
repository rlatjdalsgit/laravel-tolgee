<?php

namespace Snippet\LaravelTolgee\Console\Commands;

use Illuminate\Console\Command;
use Snippet\LaravelTolgee\Services\TolgeeService;

class SyncTranslationsCommand extends Command
{
    protected $signature = 'tolgee:translations:sync {fileNames?}';
    protected $description = 'Command will sync translations from Tolgee to local files.';

    public function __construct(private readonly TolgeeService $service)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $fileNames = $this->argument('fileNames')
            ? explode(',', $this->argument('fileNames'))
            : [];

        $result = $this->service->syncTranslations($fileNames);

        if ($result === true) {
            $this->info('✅ Translations are synced successfully!');
        } else {
            $this->error('❌ Error: Something went wrong while syncing translations.');
        }
    }
}
