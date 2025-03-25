<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;
use Illuminate\Support\Facades\Http;

class FetchLeagues extends Command
{
    protected $signature = 'fetch:leagues';
    protected $description = 'Fetch leagues from SportMonks and store them locally';

    public function handle()
    {
        $apiKey = env('SPORTMONKS_API_KEY');
        $response = Http::get("https://api.sportmonks.com/v3/football/leagues?api_token={$apiKey}");

        if ($response->successful()) {
            $leagues = $response->json()['data'];

            foreach ($leagues as $league) {
                League::updateOrCreate(
                    ['sportmonks_id' => $league['id']],
                    ['name' => $league['name']]
                );
            }

            $this->info("Leagues updated successfully!");
        } else {
            $this->error("Failed to fetch leagues!");
        }
    }
}
