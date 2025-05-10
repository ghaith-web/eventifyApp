<?php

namespace Modules\Events\Services;

use Modules\Events\Models\Category;
use Modules\Events\Repositories\CategoryRepository;

class CategoryService
{
    protected CategoryRepository $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list()
    {
        return $this->repository->all();
    }

    public function get(int $id): ?Category
    {
        return $this->repository->findById($id);
    }

    public function getBySlug(string $slug): ?Category
    {
        return $this->repository->findBySlug($slug);
    }

    public function create(array $data): Category
    {
        return $this->repository->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        return $this->repository->update($category, $data);
    }

    public function delete(Category $category): bool
    {
        return $this->repository->delete($category);
    }
}
