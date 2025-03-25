<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use Illuminate\Support\Facades\Http;

class FetchCountries extends Command
{
    protected $signature = 'fetch:countries'; // ✅ This must match the command name
    protected $description = 'Fetch countries from SportMonks and store them in the database';

    public function handle()
    {
        $apiKey = env('SPORTMONKS_API_KEY');
        $response = Http::get("https://api.sportmonks.com/v3/core/countries?api_token={$apiKey}");

        if ($response->successful()) {
            $countries = $response->json()['data'];

            foreach ($countries as $country) {
                Country::updateOrCreate(
                    ['country_id' => $country['id']], // Ensure country_id is used
                    [
                        'iso2' => $country['iso2'], 
                        'name' => $country['name']
                    ]
                );

                $this->info("Stored: {$country['name']} ({$country['iso2']}) - ID: {$country['id']}");
            }

            $this->info("All countries updated successfully!");
        } else {
            $this->error("Failed to fetch countries! Error: " . $response->body());
        }
    }
}
