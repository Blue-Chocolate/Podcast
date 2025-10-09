<?php

namespace App\Filament\Resources\PlaylistResource\Pages;

use App\Filament\Resources\PlaylistResource;
use Filament\Resources\Pages\EditRecord;

class EditPlaylist extends EditRecord
{
    protected static string $resource = PlaylistResource::class;

    protected function afterSave(): void
    {
        if (isset($this->data['episode_ids'])) {
            $this->record->episodes()->sync($this->data['episode_ids']);
        }
    }
}
