<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection($this->categoryService->listAll());
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function update(CategoryRequest $request, int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->update($id, $request->validated());
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return (new CategoryResource($category))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->categoryService->delete($id);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json(['message' => 'Category deleted']);
    }
}
