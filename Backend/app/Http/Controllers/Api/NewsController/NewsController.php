<?php

namespace App\Http\Controllers\Api\NewsController;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\NewsRepository;
use App\Actions\News\SendNewsToSubscribersAction;

class NewsController extends Controller
{
    protected $newsRepo;
    protected $sendNewsAction;

    public function __construct(NewsRepository $newsRepo, SendNewsToSubscribersAction $sendNewsAction)
    {
        $this->newsRepo = $newsRepo;
        $this->sendNewsAction = $sendNewsAction;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $news = $this->newsRepo->create($request->only('title', 'content'));
        $this->sendNewsAction->execute($news);

        return response()->json([
            'success' => true,
            'message' => 'تم نشر الخبر وإرسال الإيميلات للمشتركين!',
            'news' => $news
        ]);
    }

    public function index()
    {
        $news = $this->newsRepo->getAll();

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function show($id)
    {
        $news = $this->newsRepo->getById($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'الخبر غير موجود'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }
}
