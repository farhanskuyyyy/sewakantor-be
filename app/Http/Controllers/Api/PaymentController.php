<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?? 10;
        $page = $request->input('page') ?? 1;
        $offset = $limit * ($page - 1);
        $payments = Payment::offset($offset)
            ->limit($limit)
            ->get();
        return PaymentResource::collection($payments);
    }
}
