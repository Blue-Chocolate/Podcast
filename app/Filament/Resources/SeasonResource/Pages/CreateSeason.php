<?php

namespace App\Filament\Resources\SeasonResource\Pages;

use App\Filament\Resources\SeasonResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Episode;

class CreateSeason extends CreateRecord
{
    protected static string $resource = SeasonResource::class;

    protected function afterCreate(): void
    {
        if (!empty($this->data['episode_ids'])) {
            Episode::whereIn('id', $this->data['episode_ids'])
                ->update(['season_id' => $this->record->id]);
        }
    }
}
