<?php

namespace App\Filament\Exporters;

use App\Models\Organization;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OrganizationExporter extends Exporter
{
    protected static ?string $model = Organization::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('sector'),
            ExportColumn::make('phone'),
            ExportColumn::make('established_at'),
            ExportColumn::make('address'),
            ExportColumn::make('submission_status'),
            ExportColumn::make('created_at'),
        ];
    }
}