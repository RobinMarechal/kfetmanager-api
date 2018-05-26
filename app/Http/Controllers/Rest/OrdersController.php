<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Order;
use Carbon\Carbon;
use RobinMarechal\RestApi\Rest\QueryBuilder;
use RobinMarechal\RestApi\Rest\RestResponse;
use Symfony\Component\HttpFoundation\Response;

class OrdersController extends Controller
{
    public function all(): RestResponse
    {
        $query = QueryBuilder::prepareQuery(Order::class);

        if ($this->request->filled('fromTime')) {
            $time = Carbon::parse($this->request->get('fromTime'))->format('H:i:s');
            $query->whereRaw("TIME(`created_at`) >= '{$time}'");
        }

        if ($this->request->filled('toTime')) {
            $time = Carbon::parse($this->request->get('toTime'))->format('H:i:s');
            $query->whereRaw("TIME(`created_at`) <= '{$time}'");
        }

        $data = $query->get();

        return RestResponse::make($data, Response::HTTP_OK);
    }
}