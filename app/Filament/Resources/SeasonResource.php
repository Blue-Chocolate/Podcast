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
    protected static ?string $navigationGroup = 'إدارة البودكاست';

    // Model labels بالعربي
    public static function getModelLabel(): string
    {
        return 'موسم';
    }

    public static function getPluralModelLabel(): string
    {
        return 'المواسم';
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('podcast_id')
                    ->label('البودكاست')
                    ->relationship('podcast', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('number')
                    ->label('رقم الموسم')
                    ->required()
                    ->numeric(),

                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('release_date')
                    ->label('تاريخ الإصدار'),

                Forms\Components\Select::make('episode_ids')
                    ->label('الحلقات')
                    ->multiple()
                    ->relationship('episodes', 'title')
                    ->searchable()
                    ->preload()
                    ->helperText('اختر حلقة واحدة أو أكثر لتضمينها في هذا الموسم.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('الرقم'),
                Tables\Columns\TextColumn::make('podcast.title')->label('البودكاست'),
                Tables\Columns\TextColumn::make('episode_number')->label('رقم الموسم')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable(),
                Tables\Columns\TextColumn::make('release_date')->label('تاريخ الإصدار')->date(),
                Tables\Columns\TextColumn::make('episodes_count')->counts('episodes')->label('عدد الحلقات'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
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