<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;
use Meilisearch\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:256',
            'description' => 'required|string|max:256',
            'price' => 'required|numeric',
            'product_variant_name' => 'required|string|max:256',
            'sku' => 'required|string',
            'additional_cost' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status_code" => Response::HTTP_UNPROCESSABLE_ENTITY,
                "message" => "UNPROCESSABLE ENTITY",
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $product_variant = ProductVariant::query()
            ->where('sku', $request->get('sku'))
            ->first();


        if ($product_variant) {
            ProductVariant::query()
                ->update([
                    'name' => $request->get('product_variant_name'),
                    'additional_cost' => $request->get('additional_cost')
                ]);
        } else {
            $product_variant = ProductVariant::query()
                ->create([
                    'name' => $request->get('product_variant_name'),
                    'sku' => $request->get('sku'),
                    'additional_cost' => $request->get('additional_cost'),
                    'stock_count' => 1
                ]);
        }

        $product = Product::query()
            ->where('product_variant_id', $product_variant->id)
            ->where('name', $request->get('name'))
            ->first();

        if ($product) {
            Product::query()
                ->update([
                    'name' => $request->get('name'),
                    'description' => $request->get('description'),
                    'price' => $request->get('price')
                ]);
        } else {
            $product = Product::query()
                ->create([
                    'product_variant_id' => $product_variant->id,
                    'name' => $request->get('name'),
                    'description' => $request->get('description'),
                    'price' => $request->get('price')
                ]);

            $product_variant->increment('stock_count');
        }


        return response()->json([
            "status_code" => Response::HTTP_OK,
            "message" => "OK",
            "data" => [
                "id" => $product->id,
                "name" => $product->name,
                "description" => $product->description,
                "price" => $product->price,
                "product_variant_id" => $product->product_variant_id,
                "product_variant_name" => $product_variant->name,
                "sku" => $product_variant->sku,
                "additional_cost" => $product_variant->additional_cost,
                "stock_count" => $product_variant->stock_count,
            ]
        ], Response::HTTP_OK);
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
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'integer',
            'product_variant_id' => 'required|integer',
            'name' => 'required|string|max:256',
            'description' => 'required|string|max:256',
            'price' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status_code" => Response::HTTP_UNPROCESSABLE_ENTITY,
                "message" => "UNPROCESSABLE ENTITY",
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $product_variant = ProductVariant::query()
            ->where('id', $request->get('product_variant_id'));

        if ($product_variant->doesntExist()) {
            return response()->json([
                "status_code" => Response::HTTP_BAD_REQUEST,
                "message" => "BAD REQUEST",
            ], Response::HTTP_BAD_REQUEST);
        }

        $product = Product::query()
            ->where('id', $request->id);


        if ($product->doesntExist()) {
            return response()->json([
                "status_code" => Response::HTTP_BAD_REQUEST,
                "message" => "BAD REQUEST",
            ], Response::HTTP_BAD_REQUEST);
        }

        $product->update([
            'product_variant_id' => $request->get('product_variant_id'),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'price' => $request->get('price')
        ]);



        return response()->json([
            "status_code" => Response::HTTP_OK,
            "message" => "OK"
        ], Response::HTTP_OK);
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

        ProductVariant::query()
            ->decrement('stock_count');


        return response()->json([
            "status_code" => Response::HTTP_OK,
            "message" => "OK",
        ], Response::HTTP_OK);
    }

    // public function getMeilisearchIndex(string $index_name)
    // {
    //     $meilisearch_client = new Client(config('scout.meilisearch.host'));
    //     return $meilisearch_client->getIndex($index_name);
    // }

    // public function deleteProductFromMeilisearch(int $product_id)
    // {
    //     $products_meilisearch_index = $this->getMeilisearchIndex("products");
    //     return $products_meilisearch_index->deleteDocument($product_id);
    // }

    // public function addProductFromMeilisearch(array $product)
    // {
    //     $products_meilisearch_index = $this->getMeilisearchIndex("products");
    //     return $products_meilisearch_index->deleteDocument([$product]);
    // }

    // public function updateProductFromMeilisearch(array $product)
    // {
    //     $products_meilisearch_index = $this->getMeilisearchIndex("products");
    //     return $products_meilisearch_index->deleteDocument([$product]);
    // }
}
