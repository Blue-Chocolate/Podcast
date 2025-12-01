<?php

namespace App\Filament\Resources\PlaylistResource\Pages;

use App\Filament\Resources\PlaylistResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlaylist extends CreateRecord
{
    protected static string $resource = PlaylistResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        if (isset($this->data['episode_ids'])) {
            $this->record->episodes()->sync($this->data['episode_ids']);
        }
    }
}
