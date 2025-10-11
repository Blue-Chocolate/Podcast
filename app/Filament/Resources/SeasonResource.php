<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeasonResource\Pages;
use App\Models\Season;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeasonResource extends Resource
{
    protected static ?string $model = Season::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Podcasts Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('podcast_id')
                    ->label('Podcast')
                    ->relationship('podcast', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('number')
                    ->label('Season Number')
                    ->required()
                    ->numeric(),

                Forms\Components\TextInput::make('title')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('release_date'),

                Forms\Components\Select::make('episode_ids')
                    ->label('Episodes')
                    ->multiple()
                    ->relationship('episodes', 'title')
                    ->searchable()
                    ->preload()
                    ->helperText('Pick one or more episodes to include in this season.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('podcast.title')->label('Podcast'),
                Tables\Columns\TextColumn::make('episode_number')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('release_date')->date(),
                Tables\Columns\TextColumn::make('episodes_count')->counts('episodes')->label('Episodes'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeasons::route('/'),
            'create' => Pages\CreateSeason::route('/create'),
            'edit' => Pages\EditSeason::route('/{record}/edit'),
            'view' => Pages\ViewSeason::route('/{record}'),
        ];
    }
}
