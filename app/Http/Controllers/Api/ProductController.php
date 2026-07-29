<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(fn ($q) => $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(min($request->integer('per_page', 15), 100));

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        $this->authorize('create', Product::class);

        return new ProductResource(Product::create($request->validated()));
    }

    public function show(Product $product): ProductResource
    {
        $this->authorize('view', $product);

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $this->authorize('update', $product);

        $product->update($request->validated());

        return new ProductResource($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        if ($product->orderItems()->exists()) {
            return response()->json([
                'message' => __('This product has order records and cannot be deleted.'),
            ], 409);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}
