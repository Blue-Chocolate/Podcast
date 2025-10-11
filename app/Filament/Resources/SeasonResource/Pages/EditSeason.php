<?php

namespace App\Filament\Resources\SeasonResource\Pages;

use App\Filament\Resources\SeasonResource;
use Filament\Resources\Pages\EditRecord;

class EditSeason extends EditRecord
{
    protected static string $resource = SeasonResource::class;

    protected function afterSave(): void
    {
        if (isset($this->data['episode_ids'])) {
            $this->record->episodes()->sync($this->data['episode_ids']);
        }
    }
}
