<?php

namespace Modules\Location\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Location\Transformers\CityResource;
use Modules\Location\Transformers\CountryResource;
use Modules\Location\Services\LocationService;
use App\Helpers\ApiResponseHelper;
use Throwable;

class LocationController extends Controller
{
    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function countries()
    {
        try {
            $countries = $this->locationService->getAllCountries();
            return ApiResponseHelper::success(CountryResource::collection($countries), 'Countries loaded successfully');
        } catch (Throwable $e) {
            return ApiResponseHelper::error('Failed to load countries', 500, null, $e);
        }
    }

    public function cities($countryId)
    {
        try {
            $cities = $this->locationService->getCitiesByCountryId($countryId);
            return ApiResponseHelper::success(CityResource::collection($cities), 'Cities loaded successfully');
        } catch (Throwable $e) {
            return ApiResponseHelper::error('Failed to load cities', 500, null, $e);
        }
    }
}
