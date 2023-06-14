<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function search(Request $request)
    {
        if ($request->except('query')) {
            return response()->json([
                "status_code" => Response::HTTP_BAD_REQUEST,
                "message" => "BAD REQUEST",
            ], Response::HTTP_BAD_REQUEST);
        }


        $validator = Validator::make($request->all(), [
            'query' => 'nullable|string|max:90',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status_code" => Response::HTTP_UNPROCESSABLE_ENTITY,
                "message" => "UNPROCESSABLE ENTITY",
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $search_query = $request->get('query');

        $products = Product::search($search_query)
            ->get();

        return response()->json([
            "status_code" => Response::HTTP_OK,
            "message" => "OK",
            "data" => $products
        ], Response::HTTP_OK);

        return $products;
    }

    public function index()
    {
        $products = Product::query()
            ->with('product_variant')
            ->paginate(20);

        return $products;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
