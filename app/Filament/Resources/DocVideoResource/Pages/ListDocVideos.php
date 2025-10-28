<?php

namespace App\Filament\Resources\DocVideoResource\Pages;

use App\Filament\Resources\DocVideoResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListDocVideos extends ListRecords
{
    protected static string $resource = DocVideoResource::class;

    protected static ?string $title = 'قائمة الفيديوهات';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
