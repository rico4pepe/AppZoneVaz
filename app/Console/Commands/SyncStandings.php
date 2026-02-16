<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApiFootball\ApiFootballClient;
use App\Models\League;
use App\Models\Team;
use App\Models\Standing;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\RequestException;

class SyncStandings extends Command
{
    protected $signature = 'sync:standings {--season=2022}';

    protected $description = 'Sync standings for selected leagues';

    public function handle(ApiFootballClient $client)
    {
        $season = (int) $this->option('season');

        $this->info("Syncing standings for season {$season}");

        $targetLeagueIds = [39, 140, 135, 78, 61];

        $leagues = League::whereIn('sportmonks_id', $targetLeagueIds)->get();

        foreach ($leagues as $league) {

            try {
                $this->syncLeagueStandings($client, $league, $season);
            } catch (RequestException $e) {

                if ($e->response && $e->response->status() === 429) {
                    $this->error('Rate limit reached. Stopping safely.');
                    return Command::FAILURE;
                }

                $this->error("Failed {$league->name}");
                continue;
            }

            sleep(1);
        }

        $this->info('sync:standings completed');

        return Command::SUCCESS;
    }

    protected function syncLeagueStandings(ApiFootballClient $client, League $league, int $season): void
    {
        $response = $client->get('/standings', [
            'league' => $league->sportmonks_id,
            'season' => $season,
        ]);

       $data = [];

        if (!empty($response['response'][0]['league']['standings'])) {
            foreach ($response['response'][0]['league']['standings'] as $group) {
                foreach ($group as $row) {
                    $data[] = $row;
                }
            }
        }


        DB::transaction(function () use ($data, $league, $season) {

            foreach ($data as $row) {

                $team = Team::where('team_id', $row['team']['id'])
                    ->where('season', (string)$season)
                    ->first();

                if (!$team) {
                    continue;
                }

                Standing::updateOrCreate(
                    [
                        'league_id' => $league->id,
                        'team_id' => $team->id,
                        'season' => $season,
                    ],
                    [
                        'position' => $row['rank'],
                        'played' => $row['all']['played'],
                        'wins' => $row['all']['win'],
                        'draws' => $row['all']['draw'],
                        'losses' => $row['all']['lose'],
                        'goals_for' => $row['all']['goals']['for'],
                        'goals_against' => $row['all']['goals']['against'],
                        'goal_difference' => $row['goalsDiff'],
                        'points' => $row['points'],
                    ]
                );
            }
        });

        $this->line("Synced standings for {$league->name}");
    }
}
