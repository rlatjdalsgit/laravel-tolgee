<?php

namespace Snippet\LaravelTolgee\Console\Commands;

use Illuminate\Console\Command;
use Snippet\LaravelTolgee\Services\TolgeeService;

class ImportKeysCommand extends Command
{
    protected $signature = 'tolgee:keys:sync {fileNames?}';
    protected $description = 'Command will sync all keys from local project files to Tolgee.';

    public function __construct(private readonly TolgeeService $service)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $fileNames = $this->argument('fileNames')
            ? explode(',', $this->argument('fileNames'))
            : [];

        $response = $this->service->importKeys($fileNames);

        if ($response->successful()) {
            $this->info('✅ Keys are imported.');
        } else {
            $response->throw();
        }
    }
}
