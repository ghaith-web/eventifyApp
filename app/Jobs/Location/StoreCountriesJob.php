<?php

namespace App\Jobs\Location;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Location\Repositories\LocationRepository;

class StoreCountriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(LocationRepository $repository): void
    {
        try {
            $response = Http::timeout(15)->get('https://countriesnow.space/api/v0.1/countries');

            if (!$response->successful() || !isset($response['data']) || !is_array($response['data'])) {
                Log::error("❌ Invalid response from countries API. Status: {$response->status()}");
                return;
            }

            $countries = $response['data'];

            foreach ($countries as $item) {
                $countryName = $item['country'] ?? null;
                $cities = $item['cities'] ?? [];

                if (!$countryName || !is_array($cities)) {
                    Log::warning("⚠️ Skipping invalid country data: " . json_encode($item));
                    continue;
                }
                try {
                    $repository->storeCountryWithCities($countryName, $cities);
                } catch (\Exception $e) {
                    Log::error("❌ Failed to store '{$countryName}': " . $e->getMessage());
                }
            }
            Log::info('✅ All countries stored successfully from CountriesNow API.');
        } catch (\Exception $e) {
            Log::error("❌ Exception during countries fetch/store: {$e->getMessage()}");
        }
    }
}
