<?php

namespace App\Http\Controllers\Api\PersonController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Actions\Person\{
    IndexPersonAction,
    CreatePersonAction,
    ShowPersonAction,
    UpdatePersonAction,
    DeletePersonAction
};
use Exception;

class PersonController extends Controller
{
    public function index(IndexPersonAction $action)
    {
        try {
            return response()->json($action->execute());
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch people', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request, CreatePersonAction $action)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:people,slug',
                'role' => 'required|in:host,producer,guest,engineer,other',
                'bio' => 'nullable|string',
                'avatar_url' => 'nullable|url',
                'website' => 'nullable|url',
                'social_json' => 'nullable|array',
            ]);

            $person = $action->execute($validated);
            return response()->json($person, 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create person', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id, ShowPersonAction $action)
    {
        try {
            return response()->json($action->execute($id));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Person not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error fetching person', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id, UpdatePersonAction $action)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'slug' => "nullable|string|max:255|unique:people,slug,{$id}",
                'role' => 'sometimes|required|in:host,producer,guest,engineer,other',
                'bio' => 'nullable|string',
                'avatar_url' => 'nullable|url',
                'website' => 'nullable|url',
                'social_json' => 'nullable|array',
            ]);

            $person = $action->execute($id, $validated);
            return response()->json($person);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Person not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update person', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id, DeletePersonAction $action)
    {
        try {
            $action->execute($id);
            return response()->json(['message' => 'Person deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Person not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete person', 'message' => $e->getMessage()], 500);
        }
    }
}
