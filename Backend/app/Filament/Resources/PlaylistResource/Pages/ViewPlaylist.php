<?php

namespace App\Filament\Resources\PlaylistResource\Pages;

use App\Filament\Resources\PlaylistResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;

class ViewPlaylist extends ViewRecord implements Tables\Contracts\HasTable
{
    use InteractsWithTable; // ✅ FIX: This provides all required HasTable methods.

    protected static string $resource = PlaylistResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->record->episodes()->orderBy('pivot_ord')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('title')->label('Episode Title')->sortable(),
                Tables\Columns\TextColumn::make('duration')->label('Duration'),
                Tables\Columns\TextColumn::make('pivot.ord')->label('Order')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->emptyStateHeading('No episodes in this playlist yet.');
    }
}
