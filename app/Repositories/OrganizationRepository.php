<?php

namespace App\Repositories;

use App\Models\Organization;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class OrganizationRepository
{
    public function getAll(): Collection
    {
        return Organization::all();
    }

    public function create(array $data): Organization
    {
        try {
            return Organization::create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create organization: ' . $e->getMessage());
        }
    }

    public function find(int $id): ?Organization
    {
        return Organization::find($id);
    }
     public function createOrFindByEmail(array $data)
    {
        return Organization::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'industry' => $data['industry'] ?? null,
            ]
        );
    }
}
