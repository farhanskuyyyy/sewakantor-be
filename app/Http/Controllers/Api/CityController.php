<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CityResource;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?? 10;
        $page = $request->input('page') ?? 1;
        $offset = $limit * ($page - 1);
        $cities = City::withCount('officeSpaces')
            ->offset($offset)
            ->limit($limit)
            ->get();
        return CityResource::collection($cities);
    }

    public function show(City $city)
    {
        $city->load(['officeSpaces.city', 'officeSpaces.photos']);
        $city->loadCount('officeSpaces');
        return new CityResource($city);
    }
}
