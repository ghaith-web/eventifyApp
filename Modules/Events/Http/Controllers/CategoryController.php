<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Events\Http\Requests\CategoryRequest;
use Modules\Events\Models\Category;
use Modules\Events\Services\CategoryService;
use Modules\Events\Transformers\CategoryResource;
use App\Helpers\ApiResponseHelper;
use Throwable;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        try {
            $categories = $this->categoryService->list();
            return ApiResponseHelper::success(CategoryResource::collection($categories), 'Categories retrieved successfully');
        } catch (Throwable $e) {
            return ApiResponseHelper::error('Failed to retrieve categories', 500, null, $e);
        }
    }

    public function store(CategoryRequest $request)
    {
        try {
            $category = $this->categoryService->create($request->validated());
            return ApiResponseHelper::success(new CategoryResource($category), 'Category created successfully', 201);
        } catch (Throwable $e) {
            return ApiResponseHelper::error('Failed to create category', 500, null, $e);
        }
    }

    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $category = $this->categoryService->update($category, $request->validated());
            return ApiResponseHelper::success(new CategoryResource($category), 'Category updated successfully');
        } catch (Throwable $e) {
            return ApiResponseHelper::error('Failed to update category', 500, null, $e);
        }
    }

    public function destroy(Category $category)
    {
        try {
            $this->categoryService->delete($category);
            return ApiResponseHelper::success(null, 'Category deleted successfully', 204);
        } catch (Throwable $e) {
            return ApiResponseHelper::error('Failed to delete category', 500, null, $e);
        }
    }
}
