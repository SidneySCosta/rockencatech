<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    public function __construct(private ProductRepository $repository) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        $categoryId = isset($filters['category']) && $filters['category'] !== '' ? (int) $filters['category'] : null;
        $search     = isset($filters['search']) && $filters['search'] !== '' ? $filters['search'] : null;

        return $this->repository->paginate(12, $categoryId, $search);
    }

    public function findOrFail(int $id): Product
    {
        $product = $this->repository->findById($id);

        if (!$product) {
            throw new ModelNotFoundException("Product not found");
        }

        return $product;
    }

    public function create(array $data): Product
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->findOrFail($id);

        return $this->repository->update($product, $data);
    }

    public function delete(int $id): void
    {
        $product = $this->findOrFail($id);

        $this->repository->delete($product);
    }
}
