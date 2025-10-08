<?php

namespace App\Repositories;

use App\Models\Person;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PersonRepository
{
    public function all()
    {
        return Person::latest()->paginate(10);
    }

    public function find($id)
    {
        $person = Person::find($id);
        if (!$person) {
            throw new ModelNotFoundException('Person not found');
        }
        return $person;
    }

    public function create(array $data)
    {
        try {
            return Person::create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create person: ' . $e->getMessage());
        }
    }

    public function update($id, array $data)
    {
        $person = $this->find($id);
        try {
            $person->update($data);
            return $person;
        } catch (Exception $e) {
            throw new Exception('Failed to update person: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $person = $this->find($id);
        try {
            $person->delete();
            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to delete person: ' . $e->getMessage());
        }
    }
}
