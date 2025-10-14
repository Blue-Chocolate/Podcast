<?php

namespace App\Actions\Organization;

use App\Repositories\OrganizationRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateOrganizationAction
{
    protected $repo;

    public function __construct(OrganizationRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'sector' => 'nullable|string|max:255',
            'established_at' => 'nullable|date',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->repo->create($validator->validated());
    }
}
