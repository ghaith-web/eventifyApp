<?php

namespace Modules\Location\Services;

use App\Jobs\Location\StoreCountriesJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Location\Repositories\LocationRepository;
use Exception;

class LocationService
{
    protected LocationRepository $repository;

    public function __construct(LocationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all countries (from DB if available, fallback to API)
     */
    public function getAllCountries()
    {
        $local = $this->repository->getCountryList();
        $localCount = $local->count();

        try {
            $response = Http::timeout(10)->get('https://countriesnow.space/api/v0.1/countries');

            if ($response->successful() && isset($response['data']) && is_array($response['data'])) {
                $remoteCountries = $response['data'];
                $remoteCount = count($remoteCountries);

                if ($remoteCount > $localCount) {
                    Log::info("📦 Remote has more countries ($remoteCount vs $localCount). Dispatching update job.");
                    StoreCountriesJob::dispatch();
                } else {
                    Log::info("📦 Local country data is up to date.");
                }
            } else {
                Log::error("❌ Failed to fetch countries from API. Status: {$response->status()}");
            }
        } catch (\Exception $e) {
            Log::error("❌ Exception while checking countries from API: {$e->getMessage()}");
        }
        return $local;
    }


    /**
     * Get cities for a given country ID (API first, fallback to DB)
     */
    public function getCitiesByCountryId($countryId)
    {
        $country = $this->repository->findById($countryId);
        if (!$country) {
            throw new Exception("Country not found.");
        }

        try {
            $response = Http::timeout(10)->post('https://countriesnow.space/api/v0.1/countries/cities', [
                'country' => $country->name,
            ]);

            if ($response->successful() && isset($response['data'])) {
                return collect($response['data'])->map(function ($cityName, $index) {
                    return ['id' => $index + 1, 'name' => $cityName];
                })->toArray();
            }
        } catch (Exception $e) {
            Log::error('CountriesNow city fetch failed: ' . $e->getMessage());
        }

        return $this->repository->getCitiesByCountryId($countryId);
    }
}
