<?php

namespace App\Http\Controllers\Api\SearchController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Subscriber;
use App\Models\Episode;
use App\Models\Podcast;
use App\Models\Season;
use App\Models\Release;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى إدخال كلمة للبحث'
            ], 400);
        }

        $results = [];

        // 🔹 البحث في الأخبار
        if (class_exists(News::class)) {
            $results['news'] = News::where('title', 'LIKE', "%{$query}%")
                ->orWhere('content', 'LIKE', "%{$query}%")
                ->latest()
                ->get();
        }

        // 🔹 البحث في المشتركين
        if (class_exists(Subscriber::class)) {
            $results['subscribers'] = Subscriber::where('first_name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->get();
        }

        // 🔹 البحث في البودكاستات
        if (class_exists(Podcast::class)) {
            $results['podcasts'] = Podcast::where('title', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->latest()
                ->get();
        }

        // 🔹 البحث في المواسم
        if (class_exists(Season::class)) {
            $results['seasons'] = Season::where('title', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->latest()
                ->get();
        }

        // 🔹 البحث في الحلقات
        if (class_exists(Episode::class)) {
            $results['episodes'] = Episode::where('title', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->latest()
                ->get();
        }

        // 🔹 البحث في الإصدارات
        if (class_exists(Release::class)) {
            $results['releases'] = Release::where('title', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->latest()
                ->get();
        }

        // 🔹 لو كله فاضي
        $isEmpty = collect($results)->every(fn($r) => count($r) === 0);

        return response()->json([
            'success' => true,
            'query' => $query,
            'empty' => $isEmpty,
            'results' => $results
        ]);
    }
}
