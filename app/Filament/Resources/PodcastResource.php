<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PodcastResource\Pages;
use App\Models\Podcast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PodcastResource extends Resource
{
    protected static ?string $model = Podcast::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'بودكاست';
    }

    public static function getPluralModelLabel(): string
    {
        return 'البودكاستات';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('slug')
                    ->label('المعرف (slug)')
                    ->required()
                    ->maxLength(150),

                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('language')
                    ->label('اللغة')
                    ->required()
                    ->maxLength(10)
                    ->default('ar'),

                Forms\Components\TextInput::make('website_url')
                    ->label('رابط الموقع')
                    ->maxLength(500)
                    ->default(null),

                Forms\Components\FileUpload::make('cover_image')
                    ->label('صورة الغلاف')
                    ->image(),

                Forms\Components\TextInput::make('rss_url')
                    ->label('رابط RSS')
                    ->maxLength(500)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->label('المعرف (slug)')
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),

                Tables\Columns\TextColumn::make('language')
                    ->label('اللغة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('website_url')
                    ->label('رابط الموقع')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('صورة الغلاف'),

                Tables\Columns\TextColumn::make('rss_url')
                    ->label('رابط RSS')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPodcasts::route('/'),
            'create' => Pages\CreatePodcast::route('/create'),
            'edit' => Pages\EditPodcast::route('/{record}/edit'),
        ];
    }
}
