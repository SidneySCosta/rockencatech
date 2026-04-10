<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(Request $request): Responsable
    {
        $products = $this->productService->list($request->only(['category', 'search']));

        return new ProductCollection($products);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->findOrFail($id);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return (new ProductResource($product))->response();
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return (new ProductResource($product->load('category')))->response()->setStatusCode(201);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->update($id, $request->validated());
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return (new ProductResource($product))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->delete($id);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['message' => 'Product deleted']);
    }
}
