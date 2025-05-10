<?php

namespace Modules\Location\Services;

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
        // Step 1: Try local DB first
        $local = $this->repository->getCountryList();
        if ($local->count() > 0) {
            return $local;
        }

        // Step 2: If DB is empty, call CountriesNow API
        try {
            $res = Http::timeout(10)->get('https://countriesnow.space/api/v0.1/countries');

            if ($res->successful() && isset($res['data'])) {
                foreach ($res['data'] as $item) {
                    try {
                        $countryName = $item['country'] ?? null;
                        $cities = $item['cities'] ?? [];

                        if ($countryName && is_array($cities)) {
                            $this->repository->storeCountryWithCities($countryName, $cities);
                        } else {
                            Log::warning("Invalid data format for item: " . json_encode($item));
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to store country: {$item['country']} — " . $e->getMessage());
                    }
                }

                return $this->repository->getCountryList();
            }
        } catch (Exception $e) {
            Log::error('CountriesNow API failed: ' . $e->getMessage());
        }

        return collect([]);
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
