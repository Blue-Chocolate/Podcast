<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PodcastResource\Pages;
use App\Models\Podcast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

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
        return $form->schema([
            // 🔹 RSS Feed URL Display (auto-generated)
            Forms\Components\Placeholder::make('generated_rss_url')
                ->label('🎙️ رابط RSS المُولَّد (للتقديم إلى Apple Podcasts)')
                ->content(function ($record) {
                    if (!$record) {
                        return new HtmlString('<span style="color: #999;">احفظ البودكاست أولاً لتوليد رابط RSS</span>');
                    }

                    $rssUrl = route('rss.podcast', $record->slug);

                    return new HtmlString('
                        <div style="background: #f0f9ff; padding: 12px; border-radius: 6px; border: 2px solid #0ea5e9;">
                            <a href="' . $rssUrl . '" target="_blank" style="color: #0369a1; font-weight: 600; text-decoration: none;">
                                ' . $rssUrl . '
                            </a>
                            <div style="margin-top: 8px; font-size: 12px; color: #64748b;">
                                انسخ هذا الرابط وقدمه إلى Apple Podcasts أو Spotify
                            </div>
                        </div>
                    ');
                })
                ->columnSpanFull(),

            // 🔹 Basic Info
            Forms\Components\Section::make('معلومات البودكاست الأساسية')
                ->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('المعرف (slug)')
                        ->required()
                        ->maxLength(150)
                        ->unique(ignoreRecord: true)
                        ->regex('/^[\p{Arabic}A-Za-z0-9_-]+$/u')
                        ->helperText('سيُستخدم في رابط RSS. مثال: my-podcast'),

                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(255)
                        ->reactive()
                        ->afterStateUpdated(fn($state, callable $set) =>
                            $set('slug', Str::slug($state))
                        ),

                    Forms\Components\Textarea::make('description')
                        ->label('الوصف')
                        ->rows(4)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('language')
                        ->label('اللغة')
                        ->required()
                        ->maxLength(10)
                        ->default('ar')
                        ->helperText('كود اللغة (ar للعربية، en للإنجليزية)'),

                    Forms\Components\TextInput::make('website_url')
                        ->label('رابط الموقع')
                        ->url()
                        ->maxLength(500),
                ])
                ->columns(2),

            // 🔹 Media Section
            Forms\Components\Section::make('الوسائط')
                ->schema([
                    Forms\Components\FileUpload::make('cover_image')
                        ->label('صورة الغلاف')
                        ->image()
                        ->disk('public')
                        ->directory('covers')
                        ->visibility('public')
                        ->maxSize(10240)
                        ->nullable()
                        ->getUploadedFileNameForStorageUsing(
                            fn($file): string =>
                                now()->timestamp . '_' . Str::slug(
                                    pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                                ) . '.' . $file->getClientOriginalExtension()
                        ),
                ]),

            // 🔹 Optional external RSS section
            Forms\Components\Section::make('RSS خارجي (اختياري)')
                ->schema([
                    Forms\Components\TextInput::make('rss_url')
                        ->label('رابط RSS خارجي للدمج')
                        ->url()
                        ->maxLength(500)
                        ->helperText('إذا كان لديك بودكاست موجود، أدخل رابط RSS هنا لدمج الحلقات'),
                ])
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('cover_image')
                ->label('الغلاف')
                ->circular(),

            Tables\Columns\TextColumn::make('title')
                ->label('العنوان')
                ->searchable()
                ->weight('bold'),

            Tables\Columns\TextColumn::make('slug')
                ->label('المعرف')
                ->searchable()
                ->copyable()
                ->badge()
                ->color('info'),

            Tables\Columns\TextColumn::make('episodes_count')
                ->label('عدد الحلقات')
                ->counts('episodes')
                ->badge()
                ->color('success'),

            Tables\Columns\TextColumn::make('rss_feed')
                ->label('رابط RSS')
                ->formatStateUsing(fn($record) => route('rss.podcast', $record->slug))
                ->copyable()
                ->limit(40)
                ->tooltip(fn($record) => route('rss.podcast', $record->slug)),

            Tables\Columns\TextColumn::make('language')
                ->label('اللغة')
                ->badge(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('تاريخ الإنشاء')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->actions([
            Tables\Actions\Action::make('view_rss')
                ->label('عرض RSS')
                ->icon('heroicon-o-rss')
                ->url(fn($record) => route('rss.podcast', $record->slug))
                ->openUrlInNewTab()
                ->color('info'),

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPodcasts::route('/'),
            'create' => Pages\CreatePodcast::route('/create'),
            'edit'   => Pages\EditPodcast::route('/{record}/edit'),
        ];
    }
}
