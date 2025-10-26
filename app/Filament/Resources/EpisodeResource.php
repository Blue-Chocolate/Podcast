<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EpisodeResource\Pages;
use App\Models\Episode;
use App\Models\Season;
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
    protected static ?string $navigationGroup = 'إدارة البودكاست';

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
            // Podcast - reactive so season list updates when podcast changes
            Forms\Components\Select::make('podcast_id')
                ->label('البودكاست')
                ->required()
                ->relationship('podcast', 'title')
                ->searchable()
                ->preload()
                ->reactive()
                // clear season when podcast changed
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('season_id', null);
                }),

            // Season - options depend on selected podcast (if any), safe for create & edit
            Forms\Components\Select::make('season_id')
                ->label('الموسم')
                ->options(function (callable $get) {
                    $podcastId = $get('podcast_id');

                    $query = Season::query();

                    // If podcast selected, filter seasons by podcast (preferred UX)
                    if ($podcastId) {
                        $query->where('podcast_id', $podcastId);
                    }

                    // Order by number for nicer UX, then pluck to array
                    return $query->orderBy('number')->pluck('title', 'id')->toArray();
                })
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('اختر الموسم الذي تنتمي إليه هذه الحلقة (اختياري).'),

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
                ->maxLength(200)
                ->unique(ignoreRecord: true)
                ->lazy()
                ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

            Forms\Components\Textarea::make('description')
                ->label('الوصف')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('short_description')
                ->label('وصف مختصر')
                ->maxLength(500)
                ->nullable(),

            Forms\Components\TextInput::make('duration_seconds')
                ->label('مدة الحلقة (بالثواني)')
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

            Forms\Components\TextInput::make('views_count')
                ->label('عدد المشاهدات')
                ->numeric()
                ->default(0),

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
                ),

            // Audio upload
            Forms\Components\FileUpload::make('audio_url')
                ->label('رفع الصوت')
                ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/m4a', 'audio/wav'])
                ->disk('public')
                ->directory('episodes/audios')
                ->visibility('public')
                ->maxSize(51200)
                ->nullable()
                ->getUploadedFileNameForStorageUsing(
                    fn($file): string => now()->timestamp . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension()
                ),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('podcast.title')->label('البودكاست')->sortable(),
                Tables\Columns\TextColumn::make('season.title')->label('الموسم')->sortable(),
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
                Tables\Columns\TextColumn::make('published_at')->label('تاريخ النشر')->dateTime(),
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
