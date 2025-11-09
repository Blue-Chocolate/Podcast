<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaylistResource\Pages;
use App\Models\Playlist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class PlaylistResource extends Resource
{
    protected static ?string $model = Playlist::class;
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'إدارة البودكاست';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'قوائم التشغيل';
    protected static ?string $modelLabel = 'قائمة تشغيل';
    protected static ?string $pluralModelLabel = 'قوائم التشغيل';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات قائمة التشغيل')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('العنوان')
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
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('episode_ids')
                            ->label('الحلقات')
                            ->required()
                            ->multiple()
                            ->relationship('episodes', 'title')
                            ->preload()
                            ->searchable()
                            ->helperText('اختر حلقة أو أكثر لتضمينها في هذه القائمة')
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('created_by')
                            ->default(fn() => auth()->id()),
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
                
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('slug')
                    ->label('المعرف')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('أنشئ بواسطة')
                    ->default('غير محدد')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('episodes_count')
                    ->label('عدد الحلقات')
                    ->counts('episodes')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([])
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
            'index' => Pages\ListPlaylists::route('/'),
            'create' => Pages\CreatePlaylist::route('/create'),
            'edit' => Pages\EditPlaylist::route('/{record}/edit'),
            'view' => Pages\ViewPlaylist::route('/{record}'),
        ];
    }
}