<?php

namespace App\Http\Controllers\Api\SubmissionController;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest\StoreSubmissionRequest;
use App\Actions\Submissions\{
    CreateSubmissionAction,
    ShowSubmissionAction,
    ListSubmissionsAction,
    UpdateSubmissionStatusAction
};
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class SubmissionController extends Controller
{
    public function store(StoreSubmissionRequest $request, CreateSubmissionAction $action): JsonResponse
    {
        $result = $action->execute($request);
        return response()->json($result, Response::HTTP_CREATED);
    }

    public function show($id, ShowSubmissionAction $action): JsonResponse
    {
        $result = $action->execute($id);
        return response()->json($result['data'], $result['status']);
    }

    public function index(Request $request, ListSubmissionsAction $action): JsonResponse
    {
        $result = $action->execute($request);
        return response()->json($result['data'], $result['status']);
    }

    public function updateStatus(Request $request, $id, UpdateSubmissionStatusAction $action): JsonResponse
    {
        $result = $action->execute($id, $request);
        return response()->json($result['data'], $result['status']);
    }
}
