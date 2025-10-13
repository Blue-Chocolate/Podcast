<?php
namespace App\Repositories;

use App\Models\Organization;

class OrganizationRepository
{
    public function createOrFindByEmail(array $data): Organization
    {
        if (!empty($data['email'])) {
            return Organization::firstOrCreate(['email' => $data['email']], $data);
        }
        return Organization::create($data);
    }
}
