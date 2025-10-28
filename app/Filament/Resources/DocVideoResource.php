<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocVideoResource\Pages;
use App\Models\DocVideo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocVideoResource extends Resource
{
    protected static ?string $model = DocVideo::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationLabel = 'فيديوهات تعليمية';
    protected static ?string $pluralModelLabel = 'فيديوهات';
    protected static ?string $modelLabel = 'فيديو';

    public static function form(Form $form): Form
    {
        return $form->schema([
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
                ->relationship('category', 'name') // Links to Category model
                ->searchable(),

            Forms\Components\FileUpload::make('image_path')
                ->label('الصورة')
                ->image()
                ->directory('doc_videos/images')
                ->maxSize(2048),

            Forms\Components\FileUpload::make('video_path')
                ->label('الفيديو')
                ->directory('doc_videos/videos')
                ->acceptedFileTypes(['video/mp4', 'video/mov', 'video/avi'])
                ->required(),

            Forms\Components\TextInput::make('views_count')
                ->label('عدد المشاهدات')
                ->numeric()
                ->default(0)
                ->minValue(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label('الصورة')
                    ->square(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('عدد المشاهدات')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('حذف الكل'),
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
