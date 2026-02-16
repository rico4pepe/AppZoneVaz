<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApiFootball\ApiFootballClient;
use App\Models\League;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\RequestException;

class SyncTeams extends Command
{
    // Default season now 2022 (Free-tier safe)
    protected $signature = 'sync:teams {--season=2022}';

    protected $description = 'Sync teams per league from API-Football (season-scoped, rate-safe)';

    public function handle(ApiFootballClient $client)
    {
        $season = (int) $this->option('season');

        $this->info("Syncing teams for season {$season}");

        // 🔒 Scope to major leagues only (rate-safe)
        $targetLeagueIds = [39, 140, 135, 78, 61]; 
        // Premier League, La Liga, Serie A, Bundesliga, Ligue 1

        $leagues = League::whereIn('sportmonks_id', $targetLeagueIds)->get();

        $this->info('Processing ' . $leagues->count() . ' leagues');

        foreach ($leagues as $league) {

            try {
                $this->syncLeagueTeams($client, $league, $season);
            } catch (RequestException $e) {

                // 🛑 If rate limit hit, stop cleanly
                if ($e->response && $e->response->status() === 429) {
                    $this->error('Rate limit reached. Stopping execution safely.');
                    return Command::FAILURE;
                }

                $this->error("Failed league {$league->name}: " . $e->getMessage());
                continue;
            }

            // 🐢 Throttle to respect Free-tier limits
            sleep(1);
        }

        $this->info('sync:teams completed');

        return Command::SUCCESS;
    }

    protected function syncLeagueTeams(ApiFootballClient $client, League $league, int $season): void
    {
        $response = $client->get('/teams', [
            'league' => $league->sportmonks_id,
            'season' => $season,
        ]);

        $teams = $response['response'] ?? [];

        if (count($teams) === 0) {
            $this->line("No teams for league {$league->name} ({$league->sportmonks_id})");
            return;
        }

        DB::transaction(function () use ($teams, $league, $season) {

            foreach ($teams as $item) {

                Team::updateOrCreate(
                    [
                        'team_id' => $item['team']['id'], // provider anchor
                    ],
                    [
                        'name'      => $item['team']['name'] ?? null,
                        'country'   => $item['team']['country'] ?? null,
                        'code'      => $item['team']['code'] ?? null,
                        'season'    => (string) $season,
                        'league_id' => $league->id,
                    ]
                );
            }
        });

        $this->line("Synced " . count($teams) . " teams for {$league->name}");
    }
}
