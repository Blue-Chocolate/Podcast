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
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;
    protected static ?string $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationGroup = 'إدارة البودكاست';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'الحلقات';
    protected static ?string $pluralModelLabel = 'الحلقات';
    protected static ?string $modelLabel = 'حلقة';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('معلومات الحلقة الأساسية')
                ->schema([
                    Forms\Components\Select::make('podcast_id')
                        ->label('البودكاست')
                        ->required()
                        ->relationship('podcast', 'title')
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('season_id', null);
                        }),

                    Forms\Components\Select::make('season_id')
                        ->label('الموسم')
                        ->options(function (callable $get) {
                            $podcastId = $get('podcast_id');
                            $query = Season::query();
                            if ($podcastId) {
                                $query->where('podcast_id', $podcastId);
                            }
                            return $query->orderBy('number')->pluck('title', 'id')->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->helperText('اختر الموسم الذي تنتمي إليه هذه الحلقة (اختياري)'),

                    Forms\Components\TextInput::make('episode_number')
                        ->label('رقم الحلقة')
                        ->numeric()
                        ->required()
                        ->minValue(1),

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
                ])
                ->columns(2),

            Forms\Components\Section::make('الوصف والتفاصيل')
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->label('الوصف')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('short_description')
                        ->label('وصف مختصر')
                        ->required()
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('duration_seconds')
                        ->label('مدة الحلقة (بالثواني)')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->default(0),

                    Forms\Components\Toggle::make('explicit')
                        ->label('محتوى صريح')
                        ->default(false)
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make('حالة النشر')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'draft' => 'مسودة',
                            'published' => 'منشور',
                            'archived' => 'مؤرشف',
                        ])
                        ->required()
                        ->default('draft'),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('تاريخ النشر')
                        ->required(),

                    Forms\Components\TextInput::make('views_count')
                        ->label('عدد المشاهدات')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->default(0),
                ])
                ->columns(3),

            Forms\Components\Section::make('ملفات الوسائط')
                ->schema([
                    Forms\Components\FileUpload::make('cover_image')
                        ->label('صورة الغلاف')
                        ->image()
                        ->disk('public')
                        ->directory('covers')
                        ->visibility('public')
                        ->required()
                        ->maxSize(10240)
                        ->imageEditor()
                        ->getUploadedFileNameForStorageUsing(
                            fn($file): string => now()->timestamp . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension()
                        ),

                    Forms\Components\TextInput::make('video_url')
                        ->label('رابط الفيديو (YouTube/Vimeo)')
                        ->url()
                        ->required()
                        ->placeholder('https://www.youtube.com/watch?v=...')
                        ->helperText('أدخل رابط الفيديو من YouTube أو Vimeo أو أي منصة أخرى')
                        ->columnSpanFull()
                        ->rules([
                            'required',
                            'url',
                            function () {
                                return function (string $attribute, $value, $fail) {
                                    // Optional: Validate if it's a valid video URL
                                    $validDomains = ['youtube.com', 'youtu.be', 'vimeo.com', 'dailymotion.com', 'facebook.com', 'instagram.com'];
                                    $isValid = false;
                                    
                                    foreach ($validDomains as $domain) {
                                        if (str_contains($value, $domain)) {
                                            $isValid = true;
                                            break;
                                        }
                                    }
                                    
                                    if (!$isValid) {
                                        $fail('يجب أن يكون الرابط من منصة فيديو معروفة (YouTube, Vimeo, إلخ)');
                                    }
                                };
                            },
                        ]),

                    Forms\Components\FileUpload::make('audio_url')
                        ->label('رفع الصوت')
                        ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/m4a', 'audio/wav'])
                        ->disk('public')
                        ->directory('audios')
                        ->visibility('public')
                        ->required()
                        ->maxSize(51200)
                        ->helperText('الحد الأقصى: 50 ميجابايت')
                        ->getUploadedFileNameForStorageUsing(
                            fn($file): string => now()->timestamp . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension()
                        ),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('الرقم')
                    ->sortable(),

                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('الغلاف')
                    ->circular()
                    ->size(60),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('podcast.title')
                    ->label('البودكاست')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('season.title')
                    ->label('الموسم')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->placeholder('لا يوجد موسم'),

                Tables\Columns\TextColumn::make('episode_number')
                    ->label('رقم الحلقة')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('video_url')
                    ->label('فيديو')
                    ->icon('heroicon-o-play-circle')
                    ->url(fn($record) => $record->video_url)
                    ->openUrlInNewTab()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'danger',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                        'archived' => 'مؤرشف',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('المشاهدات')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('podcast_id')
                    ->label('البودكاست')
                    ->relationship('podcast', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('season_id')
                    ->label('الموسم')
                    ->relationship('season', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                        'archived' => 'مؤرشف',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),

                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('تم التحديث بنجاح')
                    ),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('تم الحذف بنجاح')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('حذف المحدد'),
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