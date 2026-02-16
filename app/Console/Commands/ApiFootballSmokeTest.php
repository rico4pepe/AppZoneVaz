<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApiFootball\ApiFootballClient;

class ApiFootballSmokeTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-football:smoke-test';

    /**
      The console command description.
     *
     * @var string
     */
     protected $description = 'Smoke test API-Football connectivity';

    /**
     * Execute the console command.
     *
     * @return int
     */
   public function handle(ApiFootballClient $client)
    {
        $response = $client->get('/leagues');

        $this->info('Leagues fetched: ' . count($response['response'] ?? []));
    }
}
