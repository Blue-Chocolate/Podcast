<?php

namespace App\Filament\Resources\SeasonResource\Pages;

use App\Filament\Resources\SeasonResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;

class ViewSeason extends ViewRecord implements Tables\Contracts\HasTable
{
    use InteractsWithTable;

    protected static string $resource = SeasonResource::class;

    public function mount($record = null): void
    {
        parent::mount($record);

        // Set headings as strings directly - $this->record is available after parent::mount()
        $this->heading = 'Season: ' . ($this->record->title ?: 'Season #' . $this->record->number);
        $this->subheading = $this->record->description ?: null;
    }

    public function table(Table $table): Table
    {
        return $table
            // Use getQuery() to get the underlying Builder instance
            ->query($this->record->episodes()->getQuery()->orderBy('episode_number'))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('title')->label('Episode Title')->searchable(),
                Tables\Columns\TextColumn::make('duration')->label('Duration'),
                Tables\Columns\TextColumn::make('episode_number')->label('Episode Number'),
            ])
            ->actions([
                Tables\Actions\Action::make('remove')
                    ->label('Remove')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->action(function ($record) {
                        $record->delete();
                        $this->dispatch('refresh');
                    }),
            ])
            ->emptyStateHeading('No episodes in this season yet.');
    }
}