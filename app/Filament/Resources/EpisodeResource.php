<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EpisodeResource\Pages;
use App\Models\Episode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            Forms\Components\Select::make('podcast_id')
                ->label('البودكاست')
                ->required()
                ->relationship('podcast', 'title')
                ->searchable()
                ->preload(),

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

            // ✅ New views_count field
            Forms\Components\TextInput::make('views_count')
                ->label('عدد المشاهدات')
                ->numeric()
                ->default(0)
                ->helperText('يمكنك تعديل عدد المشاهدات يدويًا أو تركه كما هو'),

            // Cover image
            Forms\Components\FileUpload::make('cover_image')
                ->label('صورة الغلاف')
                ->image()
                ->disk('public')
                ->directory('episodes/covers')
                ->visibility('public')
                ->maxSize(10240)
                ->nullable()
                ->getUploadedFileNameForStorageUsing(
                    fn($file): string => now()->timestamp . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension()
                ),

            // Video upload
            Forms\Components\FileUpload::make('video_url')
                ->label('رفع الفيديو')
                ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                ->disk('public')
                ->directory('episodes/videos')
                ->visibility('public')
                ->maxSize(512000)
                ->nullable()
                ->getUploadedFileNameForStorageUsing(
                    fn($file): string => now()->timestamp . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension()
                )
               ->saveUploadedFileUsing(function ($file, $component) {
    $filename = $component->getUploadedFileNameForStorage($file);
    Storage::disk($component->getDiskName())->putFileAs(
        $component->getDirectory(),
        $file,
        $filename,
        $component->getVisibility()
    );
    // ✅ رجّع فقط اسم الملف بدون المسار
    return $filename;
}),

            // Audio upload
            Forms\Components\FileUpload::make('audio_url')
                ->label('رفع الصوت')
                ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/m4a', 'audio/wav'])
                ->disk('public')
                ->directory('episodes/audios')
                ->visibility('public')
                ->maxSize(51200)
                ->nullable()
                ->multiple(false)
                ->getUploadedFileNameForStorageUsing(
                    fn($file): string => now()->timestamp . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension()
                )
                ->saveUploadedFileUsing(function ($file, $component) {
                    $filename = $component->getUploadedFileNameForStorage($file);
                    $path = $component->getDirectory() . '/' . $filename;
                    Storage::disk($component->getDiskName())->putFileAs(
                        $component->getDirectory(),
                        $file,
                        $filename,
                        $component->getVisibility()
                    );
                    return $path;
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('المعرف')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('عدد المشاهدات')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('الغلاف')
                    ->disk('public')
                    ->size(50),

                Tables\Columns\TextColumn::make('video_url')
                    ->label('معاينة الفيديو')
                    ->formatStateUsing(function ($state) {
                        if (!$state || $state instanceof \Closure) return '-';
                        $url = Storage::disk('public')->url($state);
                        return new HtmlString("
                            <video width='200' controls preload='metadata'>
                                <source src='{$url}' type='video/mp4'>
                                متصفحك لا يدعم تشغيل الفيديو.
                            </video>
                        ");
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('audio_url')
                    ->label('معاينة الصوت')
                    ->formatStateUsing(function ($state) {
                        if (!$state || $state instanceof \Closure) return '-';
                        $url = Storage::disk('public')->url($state);
                        return new HtmlString("
                            <audio controls preload='metadata' style='width: 200px;'>
                                <source src='{$url}' type='audio/mpeg'>
                                متصفحك لا يدعم تشغيل الصوت.
                            </audio>
                        ");
                    })
                    ->html(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
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
