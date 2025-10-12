<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaylistResource\Pages;
use App\Models\Playlist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PlaylistResource extends Resource
{
    protected static ?string $model = Playlist::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'إدارة البودكاستات';
    protected static ?string $modelLabel = 'قائمة التشغيل';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state)))
                    ->maxLength(255),

                Forms\Components\TextInput::make('slug')
                    ->label('المعرف (slug)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->rows(3)
                    ->maxLength(1000),

                Forms\Components\Select::make('episode_ids')
                    ->label('الحلقات')
                    ->multiple()
                    ->relationship('episodes', 'title')
                    ->preload()
                    ->searchable()
                    ->helperText('اختر حلقة أو أكثر لتضمينها في هذه القائمة.'),

                Forms\Components\Hidden::make('created_by')
                    ->default(fn() => auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('العنوان')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('المعرف (slug)')->searchable(),
                Tables\Columns\TextColumn::make('creator.name')->label('تم الإنشاء بواسطة')->default('N/A'),
                Tables\Columns\TextColumn::make('episodes_count')->label('عدد الحلقات')->counts('episodes'),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('حذف'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlaylists::route('/'),
            'create' => Pages\CreatePlaylist::route('/create'),
            'edit' => Pages\EditPlaylist::route('/{record}/edit'),
            'view' => Pages\ViewPlaylist::route('/{record}'),
        ];
    }
}
