<?php

namespace App\Filament\Resources\DocVideoResource\Pages;

use App\Filament\Resources\DocVideoResource;
use Filament\Resources\Pages\EditRecord;

class EditDocVideo extends EditRecord
{
    protected static string $resource = DocVideoResource::class;

    protected static ?string $title = 'تعديل الفيديو';
}
