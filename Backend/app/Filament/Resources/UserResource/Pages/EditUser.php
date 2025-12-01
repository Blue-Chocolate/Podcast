<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        Cache::forget('users.all');

        return $record;
    }

    protected function afterDelete(): void
    {
        // Clear cache after deletion
        Cache::forget('users.all');
    }
}