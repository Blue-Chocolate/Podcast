<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EpisodeResource\Pages;
use App\Models\Episode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
        return $form->schema([
            Forms\Components\TextInput::make('podcast_id')
                ->label('معرف البودكاست')
                ->required()
                ->numeric(),

            Forms\Components\TextInput::make('season_id')
                ->label('الموسم')
                ->numeric()
                ->nullable(),

            Forms\Components\TextInput::make('episode_number')
                ->label('رقم الحلقة')
                ->numeric()
                ->nullable(),

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
                ->nullable(),

            Forms\Components\TextInput::make('duration_seconds')
                ->label('مدة الحلقة (بالثواني)')
                ->required()
                ->numeric()
                ->default(0),

            Forms\Components\Toggle::make('explicit')
                ->label('محتوى صريح'),

            Forms\Components\Select::make('status')
                ->label('الحالة')
                ->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ])
                ->required(),

            Forms\Components\DateTimePicker::make('published_at')
                ->label('تاريخ النشر'),

            Forms\Components\FileUpload::make('cover_image')
                ->label('صورة الغلاف')
                ->image()
                ->disk('videos')
                ->directory('covers')
                ->visibility('public'),

            // رفع الفيديو
            Forms\Components\FileUpload::make('video_url')
    ->label('رفع الفيديو')
    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
    ->disk('videos')
    ->directory('videos')
    ->visibility('public')
    ->maxSize(512000)
    ->nullable()
    ->storeFileNamesIn('video_filename') // Store original filename
    ->getUploadedFileNameForStorageUsing(
        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
            ->prepend(now()->timestamp . '_')
    ),

Forms\Components\FileUpload::make('audio_url')
    ->label('رفع الصوت')
    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/m4a', 'audio/wav'])
    ->disk('videos')
    ->directory('audios')
    ->visibility('public')
    ->maxSize(51200)
    ->nullable()
    ->storeFileNamesIn('audio_filename')
    ->getUploadedFileNameForStorageUsing(
        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
            ->prepend(now()->timestamp . '_')
    ),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('المعرف')->searchable(),
                Tables\Columns\TextColumn::make('status')->label('الحالة'),
                Tables\Columns\TextColumn::make('published_at')->label('تاريخ النشر')->dateTime()->sortable(),

                Tables\Columns\TextColumn::make('video_url')
                    ->label('معاينة الفيديو')
                    ->formatStateUsing(function($state) {
                        if (!$state) return '-';
                        
                        $url = asset($state);
                        
                        return new HtmlString('
                            <video width="200" controls preload="metadata">
                                <source src="'.$url.'" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        ');
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('audio_url')
                    ->label('معاينة الصوت')
                    ->formatStateUsing(function($state) {
                        if (!$state) return '-';
                        
                        $url = asset($state);
                        
                        return new HtmlString('
                            <audio controls preload="metadata">
                                <source src="'.$url.'" type="audio/mpeg">
                                Your browser does not support the audio tag.
                            </audio>
                        ');
                    })
                    ->html(),
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