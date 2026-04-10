<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService
{
    public function __construct(private CategoryRepository $repository) {}

    public function listAll(): Collection
    {
        return $this->repository->all();
    }

    public function findOrFail(int $id): Category
    {
        $category = $this->repository->findById($id);

        if (!$category) {
            throw new ModelNotFoundException("Category not found");
        }

        return $category;
    }

    public function create(array $data): Category
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->findOrFail($id);

        return $this->repository->update($category, $data);
    }

    public function delete(int $id): void
    {
        $category = $this->findOrFail($id);

        $this->repository->delete($category);
    }
}
