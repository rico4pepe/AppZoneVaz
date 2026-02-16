<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApiFootball\ApiFootballClient;
use App\Models\League;
use Illuminate\Support\Facades\DB;

class SyncLeagues extends Command
{
    protected $signature = 'sync:leagues';

    protected $description = 'Sync league catalog from API-Football (metadata only)';

    public function handle(ApiFootballClient $client)
    {
        $this->info('Fetching league catalog…');

        $response = $client->get('/leagues');

        $items = $response['response'] ?? [];

        $this->info('Fetched ' . count($items) . ' leagues');

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $this->upsertLeague($item);
            }
        });

        $this->info('sync:leagues completed');

        return Command::SUCCESS;
    }

    protected function upsertLeague(array $item): void
    {
        League::updateOrCreate(
            [
                // Anchor: provider truth
                'sportmonks_id' => $item['league']['id'],
            ],
            [
                'name'         => $item['league']['name'] ?? null,
                'type'         => $item['league']['type'] ?? null, // league | cup
                'logo'         => $item['league']['logo'] ?? null,
                'country'      => $item['country']['name'] ?? null,
                'country_code' => $item['country']['code'] ?? null,
                'is_active'    => true,
            ]
        );
    }
}
