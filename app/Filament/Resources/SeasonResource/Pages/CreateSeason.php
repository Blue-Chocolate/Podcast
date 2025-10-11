<?php

namespace App\Filament\Resources\SeasonResource\Pages;

use App\Filament\Resources\SeasonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSeason extends CreateRecord
{
    protected static string $resource = SeasonResource::class;

    protected function afterCreate(): void
    {
        if (isset($this->data['episode_ids'])) {
            $this->record->episodes()->sync($this->data['episode_ids']);
        }
    }
}
