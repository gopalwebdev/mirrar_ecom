<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;
use Meilisearch\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function searchProductViaMeilisearch(Request $request)
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



        $products = Product::query()
            ->join('product_variants', 'product_variants.id', 'products.product_variant_id')
            ->where('products.name', 'like', "%{$search_query}%")
            ->orWhere('products.description', 'like', "%{$search_query}%")
            ->orWhere('product_variants.name', 'like', "%{$search_query}%")
            ->select(
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'product_variants.name as product_variant_name',
                'product_variants.sku',
                'product_variants.additional_cost',
                'product_variants.stock_count',
            )
            ->take(10)
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

        return response()->json([
            "status_code" => Response::HTTP_OK,
            "message" => "OK",
            "data" => $products
        ], Response::HTTP_OK);
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
    public function show(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status_code" => Response::HTTP_UNPROCESSABLE_ENTITY,
                "message" => "UNPROCESSABLE ENTITY",
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product = Product::query()
            ->where('id', $request->id)
            ->with('product_variant')
            ->first();


        if (!$product) {
            return response()->json([
                "status_code" => Response::HTTP_NOT_FOUND,
                "message" => "NOT FOUND",
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            "status_code" => Response::HTTP_OK,
            "message" => "OK",
            "data" => $product
        ], Response::HTTP_OK);
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
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status_code" => Response::HTTP_UNPROCESSABLE_ENTITY,
                "message" => "UNPROCESSABLE ENTITY",
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product = Product::query()
            ->where('id', $request->id);

        if ($product->doesntExist()) {
            return response()->json([
                "status_code" => Response::HTTP_NOT_FOUND,
                "message" => "NOT FOUND",
            ], Response::HTTP_NOT_FOUND);
        }

        $product->delete();

        $meilisearch_client = new Client(config('scout.meilisearch.host'));
        $meilisearch_index = $meilisearch_client->getIndex('products');

        // Remove the product document from the MeiliSearch index
        $meilisearch_index->deleteDocument($request->id);

        return response()->json([
            "status_code" => Response::HTTP_OK,
            "message" => "OK",
        ], Response::HTTP_OK);
    }
}
