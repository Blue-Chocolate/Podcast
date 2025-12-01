<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Resources\VideoResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListVideos extends ListRecords
{
    protected static string $resource = VideoResource::class;

    protected static ?string $title = 'قائمة الفيديوهات';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
