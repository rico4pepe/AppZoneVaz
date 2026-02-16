<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApiFootball\ApiFootballClient;
use App\Models\League;
use App\Models\Team;
use App\Models\Fixture; // or your Match model name
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\RequestException;

class SyncFixtures extends Command
{
    protected $signature = 'sync:fixtures {--season=2022}';

    protected $description = 'Sync fixtures for selected leagues (rate-safe, season-scoped)';

    public function handle(ApiFootballClient $client)
    {
        $season = (int) $this->option('season');

        $this->info("Syncing fixtures for season {$season}");

        $targetLeagueIds = [39, 140, 135, 78, 61];

        $leagues = League::whereIn('sportmonks_id', $targetLeagueIds)->get();

        foreach ($leagues as $league) {

            try {
                $this->syncLeagueFixtures($client, $league, $season);
            } catch (RequestException $e) {

                if ($e->response && $e->response->status() === 429) {
                    $this->error('Rate limit reached. Stopping safely.');
                    return Command::FAILURE;
                }

                $this->error("Failed league {$league->name}: " . $e->getMessage());
                continue;
            }

            sleep(1);
        }

        $this->info('sync:fixtures completed');

        return Command::SUCCESS;
    }

    protected function syncLeagueFixtures(ApiFootballClient $client, League $league, int $season): void
    {
        $response = $client->get('/fixtures', [
            'league' => $league->sportmonks_id,
            'season' => $season,
        ]);

        $fixtures = $response['response'] ?? [];

        if (count($fixtures) === 0) {
            $this->line("No fixtures for {$league->name}");
            return;
        }

        DB::transaction(function () use ($fixtures, $league, $season) {

            foreach ($fixtures as $item) {

                $homeApiId = $item['teams']['home']['id'];
                $awayApiId = $item['teams']['away']['id'];

                $homeTeam = Team::where('team_id', $homeApiId)
                    ->where('season', (string)$season)
                    ->first();

                $awayTeam = Team::where('team_id', $awayApiId)
                    ->where('season', (string)$season)
                    ->first();

                if (!$homeTeam || !$awayTeam) {
                    continue; // prevent orphan fixtures
                }

                Fixture::updateOrCreate(
                    [
                        'provider_match_id' => $item['fixture']['id'],
                    ],
                    [
                        'league_id'     => $league->id,
                        'home_team_id'  => $homeTeam->id,
                        'away_team_id'  => $awayTeam->id,
                        'kickoff_at'    => $item['fixture']['date'],
                        'status'        => $item['fixture']['status']['short'],
                        'home_score'    => $item['goals']['home'],
                        'away_score'    => $item['goals']['away'],
                    ]
                );
            }
        });

        $this->line("Synced " . count($fixtures) . " fixtures for {$league->name}");
    }
}
