<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use App\Models\BlogCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'المدونات';
    protected static ?string $modelLabel = 'مدونة';
    protected static ?string $pluralModelLabel = 'المدونات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات المدونة الأساسية')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('المستخدم')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->preload(),

                        Forms\Components\TextInput::make('title')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->required()
                            ->maxLength(500)
                            ->rows(3),

                        Forms\Components\TextInput::make('announcement')
                            ->label('الإعلان')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('الصور')
                    ->schema([
                        Forms\Components\FileUpload::make('header_image')
                            ->label('صورة الغلاف')
                            ->image()
                            ->required()
                            ->disk('public')
                            ->directory('blogs/headers')
                            ->imageEditor()
                            ->maxSize(2048),

                        Forms\Components\FileUpload::make('image')
                            ->label('الصورة الرئيسية')
                            ->image()
                            ->required()
                            ->disk('public')
                            ->directory('blogs')
                            ->imageEditor()
                            ->maxSize(2048),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('المحتوى والإعدادات')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('المحتوى')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('blog_category_id')
                            ->label('التصنيف')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->required()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('اسم التصنيف')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('الوصف')
                                    ->required()
                                    ->maxLength(500),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('نشط')
                                    ->default(true)
                                    ->required(),
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'published' => 'منشور',
                                'draft' => 'مسودة',
                                'archived' => 'مؤرشف',
                            ])
                            ->required()
                            ->default('draft'),

                        Forms\Components\DateTimePicker::make('publish_date')
                            ->label('تاريخ النشر')
                            ->required(),

                        Forms\Components\TextInput::make('views')
                            ->label('عدد المشاهدات')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->minValue(0),

                        Forms\Components\TextInput::make('footer')
                            ->label('تذييل')
                            ->required()
                            ->maxLength(255),
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

                Tables\Columns\ImageColumn::make('header_image')
                    ->label('الغلاف')
                    ->circular()
                    ->size(60),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('announcement')
                    ->label('الإعلان')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('لا يوجد تصنيف'),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->colors([
                        'success' => 'published',
                        'warning' => 'draft',
                        'secondary' => 'archived',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'published' => 'منشور',
                        'draft' => 'مسودة',
                        'archived' => 'مؤرشف',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('publish_date')
                    ->label('تاريخ النشر')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('views')
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
                Tables\Filters\SelectFilter::make('blog_category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'published' => 'منشور',
                        'draft' => 'مسودة',
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}