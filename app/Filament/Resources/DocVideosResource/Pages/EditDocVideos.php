<?php

namespace App\Filament\Resources\DocVideosResource\Pages;

use App\Filament\Resources\DocVideosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocVideos extends EditRecord
{
    protected static string $resource = DocVideosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
