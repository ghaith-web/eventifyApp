<?php

namespace Modules\Location\Repositories;

use Modules\Location\Models\Country;
use Modules\Location\Models\City;

class LocationRepository
{
    protected Country $model;

    public function __construct(Country $country)
    {
        $this->model = $country;
    }

    /**
     * Return all countries with cities
     */
    public function all()
    {
        return $this->model->with('cities')->get();
    }

    /**
     * Find a country by its ID
     */
    public function findById(int $id): ?Country
    {
        return $this->model->with('cities')->find($id);
    }

    /**
     * Create a country and optionally its cities
     */
    public function create(array $data): Country
    {
        $country = $this->model->create(['name' => $data['country']]);

        foreach ($data['cities'] ?? [] as $cityName) {
            $country->cities()->create(['name' => $cityName]);
        }

        return $country;
    }

    /**
     * Update a country's name and its cities
     */
    public function update(Country $country, array $data): Country
    {
        $country->update(['name' => $data['country']]);

        if (isset($data['cities'])) {
            $country->cities()->delete();

            foreach ($data['cities'] as $cityName) {
                $country->cities()->create(['name' => $cityName]);
            }
        }

        return $country;
    }

    /**
     * Delete a country and its cities
     */
    public function delete(Country $country): bool
    {
        $country->cities()->delete();
        return $country->delete();
    }

    /**
     * Store a country and attach cities only if not already present
     */
    public function storeCountryWithCities(string $countryName, array $cities): void
    {
        $country = $this->model->firstOrCreate(['name' => $countryName]);

        foreach ($cities as $cityName) {
            $country->cities()->firstOrCreate(['name' => $cityName]);
        }
    }

    /**
     * Get cities from the local DB by country ID
     */
    public function getCitiesByCountryId(int $countryId)
    {
        return City::where('country_id', $countryId)
                   ->select('id', 'name')
                   ->orderBy('name')
                   ->get();
    }

    /**
     * Get countries only (id and name)
     */
    public function getCountryList()
    {
        return $this->model->select('id', 'name')->orderBy('name')->get();
    }
}