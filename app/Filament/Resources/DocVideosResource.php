<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocVideoResource\Pages;
use App\Models\DocVideo;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocVideoResource extends Resource
{
    protected static ?string $model = DocVideo::class;
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->maxLength(500)
                    ->columnSpanFull(),

                Forms\Components\Select::make('category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name') // هيربط بالـ categories table
                    ->searchable()
                    ->required(),

                Forms\Components\FileUpload::make('image_path')
                    ->label('الصورة')
                    ->image()
                    ->directory('doc_videos/images'),

                Forms\Components\FileUpload::make('video_path')
                    ->label('الفيديو')
                    ->directory('doc_videos/videos')
                    ->required(),

                Forms\Components\TextInput::make('views_count')
                    ->label('عدد المشاهدات')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name') // اسم التصنيف
                    ->label('التصنيف')
                    ->searchable(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('عدد المشاهدات')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label('الصورة'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('حذف'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocVideos::route('/'),
            'create' => Pages\CreateDocVideo::route('/create'),
            'edit' => Pages\EditDocVideo::route('/{record}/edit'),
        ];
    }
}
