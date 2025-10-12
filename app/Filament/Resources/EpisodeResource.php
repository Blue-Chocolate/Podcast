<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EpisodeResource\Pages;
use App\Models\Episode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'حلقة';
    }

    public static function getPluralModelLabel(): string
    {
        return 'الحلقات';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('podcast_id')
                    ->label('معرف البودكاست')
                    ->required()
                    ->numeric(),

                Forms\Components\TextInput::make('season_id')
                    ->label('الموسم')
                    ->numeric()
                    ->default(null),

                Forms\Components\TextInput::make('transcript_id')
                    ->label('معرف النص')
                    ->numeric()
                    ->default(null),

                Forms\Components\TextInput::make('episode_number')
                    ->label('رقم الحلقة')
                    ->numeric()
                    ->default(null),

                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('slug')
                    ->label('المعرف (slug)')
                    ->required()
                    ->maxLength(200),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('short_description')
                    ->label('وصف مختصر')
                    ->maxLength(500)
                    ->default(null),

                Forms\Components\TextInput::make('duration_seconds')
                    ->label('مدة الحلقة (بالثواني)')
                    ->required()
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('explicit')
                    ->label('محتوى صريح')
                    ->required(),

                Forms\Components\TextInput::make('status')
                    ->label('الحالة')
                    ->required(),

                Forms\Components\DateTimePicker::make('published_at')
                    ->label('تاريخ النشر'),

                Forms\Components\FileUpload::make('cover_image')
                    ->label('صورة الغلاف')
                    ->image(),

                Forms\Components\TextInput::make('audio_url')
                    ->label('رابط الصوت')
                    ->maxLength(255)
                    ->default(null),

                Forms\Components\TextInput::make('video_url')
                    ->label('رابط الفيديو')
                    ->maxLength(255)
                    ->default(null),

                Forms\Components\TextInput::make('file_size')
                    ->label('حجم الملف')
                    ->numeric()
                    ->default(null),

                Forms\Components\TextInput::make('mime_type')
                    ->label('نوع الملف')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('podcast_id')->label('معرف البودكاست')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('season_id')->label('الموسم')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('episode_number')->label('رقم الحلقة')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('المعرف (slug)')->searchable(),
                Tables\Columns\TextColumn::make('duration_seconds')->label('المدة (ثواني)')->numeric()->sortable(),
                Tables\Columns\IconColumn::make('explicit')->label('محتوى صريح')->boolean(),
                Tables\Columns\TextColumn::make('status')->label('الحالة'),
                Tables\Columns\TextColumn::make('published_at')->label('تاريخ النشر')->dateTime()->sortable(),
                Tables\Columns\ImageColumn::make('cover_image')->label('صورة الغلاف'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEpisodes::route('/'),
            'create' => Pages\CreateEpisode::route('/create'),
            'view' => Pages\ViewEpisode::route('/{record}'),
            'edit' => Pages\EditEpisode::route('/{record}/edit'),
        ];
    }
}
