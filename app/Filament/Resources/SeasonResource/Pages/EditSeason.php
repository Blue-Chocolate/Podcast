<?php

namespace App\Filament\Resources\SeasonResource\Pages;

use App\Filament\Resources\SeasonResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\Episode;

class EditSeason extends EditRecord
{
    protected static string $resource = SeasonResource::class;

    protected function afterSave(): void
    {
        if (!empty($this->data['episode_ids'])) {
            // Remove old links
            Episode::where('season_id', $this->record->id)
                ->update(['season_id' => null]);

            // Add new ones
            Episode::whereIn('id', $this->data['episode_ids'])
                ->update(['season_id' => $this->record->id]);
        }
    }
}
