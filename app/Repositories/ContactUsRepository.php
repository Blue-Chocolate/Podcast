<?php

namespace App\Repositories;

use App\Models\ContactUs;

class ContactUsRepository
{
    public function getAll()
    {
        return ContactUs::latest()->get();
    }

    public function findById($id)
    {
        return ContactUs::findOrFail($id);
    }

    public function create(array $data)
    {
        return ContactUs::create($data);
    }

    public function delete($id)
    {
        $contact = ContactUs::findOrFail($id);
        return $contact->delete();
    }
}
