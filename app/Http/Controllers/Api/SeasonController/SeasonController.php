<?php

namespace App\Http\Controllers\Api\SeasonController;

use App\Http\Controllers\Controller;
use App\Repositories\SeasonRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SeasonController extends Controller
{
    protected SeasonRepository $repository;

    public function __construct(SeasonRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * GET /api/seasons
     * Get all seasons with podcasts and episodes
     */
    public function index(): JsonResponse
    {
        $seasons = $this->repository->all();
        return response()->json($seasons);
    }

    /**
     * GET /api/seasons/{id}
     * Get a single season with its episodes
     */
    public function show(int $id): JsonResponse
    {
        try {
            $season = $this->repository->find($id);
            return response()->json($season);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Season not found'], 404);
        }
    }

    /**
     * POST /api/seasons
     * Create a new season
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'podcast_id' => 'required|exists:podcasts,id',
            'number' => 'required|integer',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
        ]);

        $season = $this->repository->create($validated);

        return response()->json($season, 201);
    }

    /**
     * PUT /api/seasons/{id}
     * Update a season
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'number' => 'sometimes|integer',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
        ]);

        $season = $this->repository->update($id, $validated);

        return response()->json($season);
    }

    /**
     * DELETE /api/seasons/{id}
     * Delete a season (and cascade episodes)
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return response()->json(['message' => 'Season deleted successfully']);
    }
}
