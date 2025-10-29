<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReleaseResource\Pages;
use App\Models\Release;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReleaseResource extends Resource
{
    protected static ?string $model = Release::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getModelLabel(): string
    {
        return 'إصدار';
    }

    public static function getPluralModelLabel(): string
    {
        return 'الإصدارات';
    }

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
                    ->rows(3)
                    ->columnSpanFull(),

               Forms\Components\FileUpload::make('file_path')
    ->label('ملف PDF')
    ->disk('public')
    ->directory('files')
    ->acceptedFileTypes(['application/pdf'])
    ->maxSize(10240)
    ->required()
    ->openable()
    ->downloadable(),

                Forms\Components\FileUpload::make('images')
                    ->label('صور الغلاف')
                    ->disk('public')
                    ->directory('releases/images')
                    ->image()
                    ->multiple() 
                    ->maxSize(2048)
                    ->imageEditor(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\ImageColumn::make('images.0') // يعرض أول صورة
                    ->label('الغلاف')
                    ->disk('public')
                    ->size(50),

                Tables\Columns\TextColumn::make('file_path')
                    ->label('ملف PDF')
                    ->formatStateUsing(fn ($state) => $state
                        ? new HtmlString('<a href="' . Storage::disk('public')->url($state) . '" target="_blank">تحميل PDF</a>')
                        : '-'
                    )
                    ->html(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->searchable()
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReleases::route('/'),
            'create' => Pages\CreateRelease::route('/create'),
            'edit' => Pages\EditRelease::route('/{record}/edit'),
        ];
    }
}
