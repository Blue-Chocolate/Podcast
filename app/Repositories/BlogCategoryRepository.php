<?php

namespace App\Repositories;

use App\Models\BlogCategory;

class BlogCategoryRepository
{
    public function getAll($limit = 10)
    {
        return BlogCategory::with(['blogs' => function ($q) {
            $q->latest();
        }])
        ->select([
            'id',
            'name',
            'description',
            'header_image',
            'image_path',
            'slug',
            'is_active',
            'views_count',
            'created_at',
            'updated_at'
        ])
        ->paginate($limit);
    }

    public function getById($id, $limit = 10)
    {
        $category = BlogCategory::select([
            'id',
            'name',
            'description',
            'header_image',
            'image_path',
            'slug',
            'is_active',
            'views_count',
            'created_at',
            'updated_at'
        ])->find($id);

        if (!$category) {
            return null;
        }

        $blogs = $category->blogs()
            ->select([
                'id',
                'user_id',
                'title',
                'description',
                'content',
                'header_image',
                'image',
                'announcement',
                'footer',
                'status',
                'publish_date',
                'views',
                'blog_category_id',
                'created_at',
                'updated_at'
            ])
            ->latest()
            ->paginate($limit);

        return [
            'category' => $category,
            'blogs' => $blogs
        ];
    }

    public function getBlogByCategory($categoryId, $blogId)
    {
        $category = BlogCategory::with(['blogs' => function ($q) use ($blogId) {
            $q->where('id', $blogId);
        }])->find($categoryId, [
            'id',
            'name',
            'description',
            'header_image',
            'image_path',
            'slug',
            'is_active',
            'views_count',
            'created_at',
            'updated_at'
        ]);

        return $category ? [
            'category' => $category->only([
                'id', 'name', 'description', 'header_image', 'image_path', 'slug',
                'is_active', 'views_count', 'created_at', 'updated_at'
            ]),
            'blog' => $category->blogs->first()
        ] : null;
    }
}
