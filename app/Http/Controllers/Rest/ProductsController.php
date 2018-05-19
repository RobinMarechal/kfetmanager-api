<?php
namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Product;

use Illuminate\Http\Response;
use RobinMarechal\RestApi\Rest\RestResponse;

class ProductsController extends Controller
{
    public function getCategory($id): RestResponse
    {
        $product = Product::with('subcategory.category')->findOrFail($id);

        return RestResponse::make($product->subcategory->category, Response::HTTP_OK);
    }
}