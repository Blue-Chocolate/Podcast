<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions;
use Filament\Tables\Columns;
use Illuminate\Database\Eloquent\Builder;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'الأخبار';
    protected static ?string $pluralModelLabel = 'الأخبار';
    protected static ?string $modelLabel = 'خبر';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('عنوان الخبر')
                    ->required()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('image')
                    ->label('صورة الخبر')
                    ->directory('news-images')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048),

                Forms\Components\Textarea::make('content')
                    ->label('محتوى الخبر')
                    ->required()
                    ->rows(8),

                Forms\Components\TextInput::make('author')
                    ->label('اسم الكاتب')
                    ->default(auth()->user()->name ?? 'مدير الموقع'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('id')->label('ID')->sortable(),
                Columns\ImageColumn::make('image')->label('الصورة')->circular(),
                Columns\TextColumn::make('title')->label('العنوان')->searchable()->sortable(),
                Columns\TextColumn::make('author')->label('الكاتب')->sortable(),
                Columns\TextColumn::make('created_at')
                    ->label('تاريخ النشر')
                    ->dateTime('d M Y - H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
