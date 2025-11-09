<?php 


namespace App\Filament\Resources;

use App\Filament\Resources\VideoCategoryResource\Pages;
use App\Models\VideoCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class VideoCategoryResource extends Resource
{
    protected static ?string $model = VideoCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'إدارة الفيديوهات';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'تصنيفات الفيديو';
    protected static ?string $modelLabel = 'تصنيف';
    protected static ?string $pluralModelLabel = 'التصنيفات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات التصنيف')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم التصنيف')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label('المعرف (slug)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('image_path')
                            ->label('صورة التصنيف')
                            ->image()
                            ->required()
                            ->disk('public')
                            ->directory('category-images')
                            ->imageEditor()
                            ->maxSize(2048),

                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->required()
                            ->default(true),

                        Forms\Components\TextInput::make('views_count')
                            ->label('عدد المشاهدات')
                            ->required()
                            ->numeric()
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
                    ->circular()
                    ->size(60),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('slug')
                    ->label('المعرف')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->label('المشاهدات')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('نشط')
                    ->falseLabel('غير نشط'),
            ])
            ->actions([
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
            'index' => Pages\ListVideoCategories::route('/'),
            'create' => Pages\CreateVideoCategory::route('/create'),
            'edit' => Pages\EditVideoCategory::route('/{record}/edit'),
        ];
    }
}