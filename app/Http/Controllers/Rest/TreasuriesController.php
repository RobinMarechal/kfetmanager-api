<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Treasury;
use Illuminate\Support\Facades\Request;

class TreasuriesController extends Controller
{
    public function all()
    {
        $request = Request::instance();
        $url = $request->url();

        if (ends_with($url, "treasury")) {
            if ($request->has('details') && $request->get('details') == 'true') {
                $treasury = Treasury::orderBy('id', 'desc')->limit(1)->first();

                if (!$treasury) return null;

                $model = 'App\\' . substr(camel_case(strtolower('A_' . $treasury->movement_type)), 1);
                $treasury->details = $model::find($treasury->movement_id);

                return $treasury;
            }

            return Treasury::orderBy('id', 'desc')->limit(1)->first();
        }

        return parent::all();
    }
}