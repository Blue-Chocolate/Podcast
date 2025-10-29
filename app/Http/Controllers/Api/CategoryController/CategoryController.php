<?php

namespace App\Http\Controllers\Api\CategoryController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

use App\Actions\Categories\CreateCategoryAction;
use App\Actions\Categories\UpdateCategoryAction;
use App\Actions\Categories\DeleteCategoryAction;
use App\Actions\Categories\GetAllCategoriesAction;
use App\Actions\Categories\GetCategoryByIdAction;


class CategoryController extends Controller
{
    protected $createCategory;
    protected $updateCategory;
    protected $deleteCategory;
    protected $getAllCategories;
    protected $getCategoryById;

    public function __construct(
        CreateCategoryAction $createCategory,
        UpdateCategoryAction $updateCategory,
        DeleteCategoryAction $deleteCategory,
        GetAllCategoriesAction $getAllCategories,
        GetCategoryByIdAction $getCategoryById
    ) {
        $this->createCategory = $createCategory;
        $this->updateCategory = $updateCategory;
        $this->deleteCategory = $deleteCategory;
        $this->getAllCategories = $getAllCategories;
        $this->getCategoryById = $getCategoryById;
    }

    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
{
    try {
        $limit = $request->query('limit', 10);
        $categories = \App\Models\Category::paginate($limit);
        return response()->json($categories);
    } catch (Exception $e) {
        return response()->json(['error' => 'Failed to fetch categories', 'message' => $e->getMessage()], 500);
    }
}

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|unique:categories,slug',
                'description' => 'nullable|string',
            ]);

            $category = $this->createCategory->execute($validated);
            return response()->json(['message' => 'Category created successfully', 'data' => $category], 201);

        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create category', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified category.
     */
    public function show($id)
    {
        try {
            $category = $this->getCategoryById->execute($id);
            return response()->json($category);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch category', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|unique:categories,slug,' . $id,
                'description' => 'nullable|string',
            ]);

            $category = $this->updateCategory->execute($id, $validated);
            return response()->json(['message' => 'Category updated successfully', 'data' => $category]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update category', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        try {
            $this->deleteCategory->execute($id);
            return response()->json(['message' => 'Category deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete category', 'message' => $e->getMessage()], 500);
        }
    }
}
