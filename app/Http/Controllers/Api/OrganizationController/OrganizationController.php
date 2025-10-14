<?php

namespace App\Http\Controllers\API\OrganizationController;

use App\Http\Controllers\Controller;
use App\Actions\Organization\CreateOrganizationAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class OrganizationController extends Controller
{
    protected $createAction;

    public function __construct(CreateOrganizationAction $createAction)
    {
        $this->createAction = $createAction;
    }

    public function store(Request $request)
    {
        try {
            $organization = $this->createAction->execute($request->all());

            return response()->json([
                'message' => 'Organization created successfully',
                'data' => $organization
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
