<?php

namespace App\Repositories;

use App\Models\News;
use Illuminate\Support\Facades\Cache;

class NewsRepository
{
    public function create(array $data): News
    {
        $news = News::create($data);
        Cache::forget('all_news'); // امسح الكاش عند إنشاء خبر جديد
        return $news;
    }

    public function getAll()
    {
        return Cache::remember('all_news', 60 * 10, function () {
            // يخزن الكاش لمدة 10 دقائق
            return News::latest()->get();
        });
    }

    public function getById($id): ?News
    {
        return Cache::remember("news_{$id}", 60 * 10, function () use ($id) {
            return News::find($id);
        });
    }
}
