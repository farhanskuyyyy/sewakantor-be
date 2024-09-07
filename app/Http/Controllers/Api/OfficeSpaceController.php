<?php

namespace App\Http\Controllers\Api;

use App\Models\OfficeSpace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OfficeSpaceResource;

class OfficeSpaceController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?? 10;
        $page = $request->input('page') ?? 1;
        $offset = $limit * ($page - 1);
        $officeSpaces = OfficeSpace::with('city')
            ->offset($offset)
            ->limit($limit)
            ->get();
        return OfficeSpaceResource::collection($officeSpaces);
    }

    public function show(OfficeSpace $officeSpace)
    {
        $officeSpace->load(['city', 'photos', 'benefits']);
        return new OfficeSpaceResource($officeSpace);
    }
}
