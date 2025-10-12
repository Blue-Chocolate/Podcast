<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Labels النموذج بالعربي
    public static function getModelLabel(): string
    {
        return 'منشور';
    }

    public static function getPluralModelLabel(): string
    {
        return 'المنشورات';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->label('معرّف المستخدم')
                    ->required()
                    ->numeric(),

                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('content')
                    ->label('المحتوى')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('category')
                    ->label('الفئة')
                    ->maxLength(100)
                    ->default(null),

                Forms\Components\TextInput::make('status')
                    ->label('الحالة')
                    ->required(),

                Forms\Components\DateTimePicker::make('publish_date')
                    ->label('تاريخ النشر'),

                Forms\Components\TextInput::make('views')
                    ->label('عدد المشاهدات')
                    ->required()
                    ->numeric()
                    ->default(0),

                Forms\Components\FileUpload::make('image')
                    ->label('صورة المنشور')
                    ->image(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->label('معرّف المستخدم')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('الفئة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة'),

                Tables\Columns\TextColumn::make('publish_date')
                    ->label('تاريخ النشر')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('views')
                    ->label('عدد المشاهدات')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
        return [
            //
        ];
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
