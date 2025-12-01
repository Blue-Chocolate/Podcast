<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactUsResource\Pages;
use App\Models\ContactUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ContactUsResource extends Resource
{
    protected static ?string $model = ContactUs::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'إدارة الموقع';
    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'رسالة تواصل';
    protected static ?string $pluralModelLabel = 'رسائل التواصل';
    protected static ?string $navigationLabel = 'رسائل اتصل بنا';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('معلومات المرسل')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('الاسم')
                        ->required()
                        ->maxLength(255)
                        ->disabled(),
                    
                    Forms\Components\TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->required()
                        ->disabled(),
                    
                    Forms\Components\TextInput::make('subject')
                        ->label('الموضوع')
                        ->required()
                        ->maxLength(255)
                        ->disabled(),
                ])
                ->columns(2),

            Forms\Components\Section::make('محتوى الرسالة')
                ->schema([
                    Forms\Components\Textarea::make('message')
                        ->label('الرسالة')
                        ->rows(8)
                        ->required()
                        ->disabled()
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
                
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('subject')
                    ->label('الموضوع')
                    ->limit(30)
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('message')
                    ->label('الرسالة')
                    ->limit(50)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الاستلام')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),
                
                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('تم الحذف بنجاح')
                    )
                    ->failureNotification(
                        Notification::make()
                            ->danger()
                            ->title('فشل الحذف')
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
            'index' => Pages\ListContactUs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}