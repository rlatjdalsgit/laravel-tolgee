<?php

namespace Snippet\LaravelTolgee\Console\Commands;

use Illuminate\Console\Command;
use Snippet\LaravelTolgee\Services\TolgeeService;

class DeleteAllKeysCommand extends Command
{
    protected $signature = 'tolgee:keys:flush';
    protected $description = 'Command will delete all keys in Tolgee project.';

    public function __construct(private readonly TolgeeService $service)
    {
        parent::__construct();
    }

    public function handle()
    {
        $response = $this->service->deleteKeys();

        if ($response->successful()) {
            $this->info('All keys are deleted.');
        } else {
            $response->throw();
        }
    }
}
