<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CategoryRepository
{
    public function getAll()
    {
        return Category::latest()->paginate(10);
    }

    public function find($id)
    {
        $category = Category::find($id);

        if (!$category) {
            throw new ModelNotFoundException('Category not found.');
        }

        return $category;
    }

    public function create(array $data)
    {
        try {
            return Category::create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create category: ' . $e->getMessage());
        }
    }

    public function update($id, array $data)
    {
        $category = $this->find($id);

        try {
            $category->update($data);
            return $category;
        } catch (Exception $e) {
            throw new Exception('Failed to update category: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $category = $this->find($id);

        try {
            $category->delete();
            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to delete category: ' . $e->getMessage());
        }
    }
}
