<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'المنشورات';
    protected static ?string $modelLabel = 'منشور';
    protected static ?string $pluralModelLabel = 'المنشورات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات المنشور')
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

                        Forms\Components\TextInput::make('category')
                            ->label('الفئة')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->required()
                            ->options([
                                'draft' => 'مسودة',
                                'published' => 'منشور',
                                'archived' => 'مؤرشف',
                            ])
                            ->default('draft'),

                        Forms\Components\DateTimePicker::make('publish_date')
                            ->label('تاريخ النشر')
                            ->required(),

                        Forms\Components\Textarea::make('content')
                            ->label('المحتوى')
                            ->required()
                            ->rows(8)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('image')
                            ->label('صورة المنشور')
                            ->image()
                            ->required()
                            ->disk('public')
                            ->directory('posts')
                            ->imageEditor()
                            ->maxSize(2048),

                        Forms\Components\TextInput::make('views')
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

                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular()
                    ->size(60),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category')
                    ->label('الفئة')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->colors([
                        'success' => 'published',
                        'warning' => 'draft',
                        'secondary' => 'archived',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'draft' => 'مسودة',
                        'published' => 'منشور',
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                        'archived' => 'مؤرشف',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->label('الفئة')
                    ->options(fn() => \App\Models\Post::distinct()->pluck('category', 'category')->toArray()),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}