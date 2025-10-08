<?php

namespace App\Http\Controllers\Api\SeasonController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Season\ListSeasonsAction;
use App\Actions\Season\ShowSeasonAction;
use App\Actions\Season\CreateSeasonAction;
use App\Actions\Season\UpdateSeasonAction;
use App\Actions\Season\DeleteSeasonAction;

class SeasonController extends Controller
{
    public function index(ListSeasonsAction $listAction)
    {
        return response()->json($listAction->execute());
    }

    public function show(int $id, ShowSeasonAction $showAction)
    {
        return response()->json($showAction->execute($id));
    }

    public function store(Request $request, CreateSeasonAction $createAction)
    {
        $data = $request->validate([
            'podcast_id' => 'required|exists:podcasts,id',
            'number' => 'required|integer',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
        ]);

        return response()->json($createAction->execute($data), 201);
    }

    public function update(Request $request, int $id, UpdateSeasonAction $updateAction)
    {
        $data = $request->validate([
            'podcast_id' => 'sometimes|exists:podcasts,id',
            'number' => 'sometimes|integer',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
        ]);

        return response()->json($updateAction->execute($id, $data));
    }

    public function destroy(int $id, DeleteSeasonAction $deleteAction)
    {
        $deleteAction->execute($id);
        return response()->json(['message' => 'Season deleted successfully']);
    }
}
