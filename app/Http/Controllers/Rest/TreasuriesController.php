<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Treasury;
use Illuminate\Support\Facades\Request;
use RobinMarechal\RestApi\Rest\RestResponse;
use Symfony\Component\HttpFoundation\Response;

class TreasuriesController extends Controller
{
    public function getLast()
    {
        $treasury = null;

        if ($this->request->has('details') && $this->request->get('details') == 'true') {
            $treasury = Treasury::orderBy('id', 'desc')->limit(1)->first();

            if (!$treasury) return null;

            $model = 'App\\' . substr(camel_case(strtolower('A_' . $treasury->movement_type)), 1);
            $treasury->details = $model::find($treasury->movement_id);
        }

        $treasury = Treasury::orderBy('id', 'desc')->limit(1)->first();

        return RestResponse::make($treasury, Response::HTTP_OK)->toJsonResponse();
    }
}