<?php
namespace App\Services\ApiFootball;

use Illuminate\Support\Facades\Http;

class ApiFootballClient
{
    public function get(string $endpoint, array $params = [])
    {
        return Http::withHeaders([
                'x-apisports-key' => config('api_football.key'),
            ])
            ->timeout(config('api_football.timeout'))
            ->get(config('api_football.base_url') . $endpoint, $params)
            ->throw()
            ->json();
    }
}
