<?php

namespace App\Http\Controllers\Api\ContactUsController;

use App\Http\Controllers\Controller;
use App\Repositories\ContactUsRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContactUsController extends Controller
{
    protected $contactRepo;

    public function __construct(ContactUsRepository $contactRepo)
    {
        $this->contactRepo = $contactRepo;
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->contactRepo->getAll(),
        ]);
    }

    public function show($id)
    {
        $contact = $this->contactRepo->findById($id);
        return response()->json(['success' => true, 'data' => $contact]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:255',
                'subject' => 'nullable|string|max:255',
                'message' => 'required|string',
            ]);

            $contact = $this->contactRepo->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Message submitted successfully',
                'data' => $contact,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy($id)
    {
        $this->contactRepo->delete($id);
        return response()->json(['success' => true, 'message' => 'Message deleted']);
    }
}
