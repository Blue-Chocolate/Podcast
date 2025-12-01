<?php 

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationGroup = 'إدارة الفيديوهات';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'الفيديوهات';
    protected static ?string $pluralModelLabel = 'الفيديوهات';
    protected static ?string $modelLabel = 'فيديو';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('معلومات الفيديو')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('video_category_id')
                        ->label('التصنيف')
                        ->relationship('category', 'name', fn($query) => $query->where('is_active', true))
                        ->searchable()
                        ->required()
                        ->preload(),

                    Forms\Components\Textarea::make('description')
                        ->label('الوصف')
                        ->required()
                        ->maxLength(500)
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('image_path')
                        ->label('صورة الغلاف')
                        ->disk('public')
                        ->directory('images')
                        ->image()
                        ->required()
                        ->maxSize(2048)
                        ->imageEditor(),

                    Forms\Components\TextInput::make('video_path')
                        ->label('رابط الفيديو (YouTube/Vimeo)')
                        ->url()
                        ->required()
                        ->placeholder('https://www.youtube.com/watch?v=...')
                        ->helperText('أدخل رابط الفيديو من YouTube أو Vimeo أو أي منصة أخرى')
                        ->rules([
                            'required',
                            'url',
                            function () {
                                return function (string $attribute, $value, $fail) {
                                    // Optional: Validate if it's a valid video URL
                                    $validDomains = ['youtube.com', 'youtu.be', 'vimeo.com', 'dailymotion.com'];
                                    $isValid = false;
                                    
                                    foreach ($validDomains as $domain) {
                                        if (str_contains($value, $domain)) {
                                            $isValid = true;
                                            break;
                                        }
                                    }
                                    
                                    if (!$isValid) {
                                        $fail('يجب أن يكون الرابط من YouTube أو Vimeo أو Dailymotion');
                                    }
                                };
                            },
                        ]),

                    Forms\Components\TextInput::make('views_count')
                        ->label('عدد المشاهدات')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->minValue(0),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('الرقم')
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('الصورة')
                    ->square()
                    ->size(60),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\IconColumn::make('video_path')
                    ->label('رابط')
                    ->icon('heroicon-o-link')
                    ->url(fn($record) => $record->video_path)
                    ->openUrlInNewTab(),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->label('المشاهدات')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('video_category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}