<?php 

namespace App\Filament\Resources;

use App\Filament\Resources\ReleaseResource\Pages;
use App\Models\Release;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;

class ReleaseResource extends Resource
{
    protected static ?string $model = Release::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'الإصدارات';
    protected static ?string $modelLabel = 'إصدار';
    protected static ?string $pluralModelLabel = 'الإصدارات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الإصدار')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->required()
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
                            ->downloadable()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('images')
                            ->label('صور الغلاف')
                            ->disk('public')
                            ->directory('releases/images')
                            ->image()
                            ->multiple()
                            ->required()
                            ->maxSize(2048)
                            ->imageEditor()
                            ->maxFiles(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('الرقم')
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('images.0')
                    ->label('الغلاف')
                    ->disk('public')
                    ->circular()
                    ->size(60),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('file_path')
                    ->label('ملف PDF')
                    ->formatStateUsing(fn($state) => $state
                        ? new HtmlString('<a href="' . Storage::disk('public')->url($state) . '" target="_blank" class="text-blue-600 hover:underline">تحميل PDF</a>')
                        : '-'
                    )
                    ->html(),
                
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
            'index' => Pages\ListReleases::route('/'),
            'create' => Pages\CreateRelease::route('/create'),
            'edit' => Pages\EditRelease::route('/{record}/edit'),
        ];
    }
}
