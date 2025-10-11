<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EpisodeResource\Pages;
use App\Filament\Resources\EpisodeResource\RelationManagers;
use App\Models\Episode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('podcast_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('season_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('transcript_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('episode_number')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(200),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('short_description')
                    ->maxLength(500)
                    ->default(null),
                Forms\Components\TextInput::make('duration_seconds')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('explicit')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\DateTimePicker::make('published_at'),
                Forms\Components\FileUpload::make('cover_image')
                    ->image(),
                Forms\Components\TextInput::make('audio_url')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('video_url')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('file_size')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('mime_type')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('podcast_id')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('season_id')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('episode_number')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('title')
                ->searchable(),
            Tables\Columns\TextColumn::make('slug')
                ->searchable(),
            Tables\Columns\TextColumn::make('duration_seconds')
                ->numeric()
                ->sortable(),
            Tables\Columns\IconColumn::make('explicit')->boolean(),
            Tables\Columns\TextColumn::make('status'),
            Tables\Columns\TextColumn::make('published_at')
                ->dateTime()
                ->sortable(),
            Tables\Columns\ImageColumn::make('cover_image'),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\ViewAction::make(), // 👈 add view button
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}

public static function getPages(): array
{
    return [
        'index' => Pages\ListEpisodes::route('/'),
        'create' => Pages\CreateEpisode::route('/create'),
        'view' => Pages\ViewEpisode::route('/{record}'), // 👈 add view route
        'edit' => Pages\EditEpisode::route('/{record}/edit'),
    ];
}
}
