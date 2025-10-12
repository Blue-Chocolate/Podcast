<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'شخص';
    }

    public static function getPluralModelLabel(): string
    {
        return 'الأشخاص';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('الاسم')->required()->maxLength(255),
                Forms\Components\TextInput::make('slug')->label('المعرف (slug)')->maxLength(255)->default(null),
                Forms\Components\TextInput::make('role')->label('الدور')->required(),
                Forms\Components\Textarea::make('bio')->label('السيرة')->columnSpanFull(),
                Forms\Components\TextInput::make('avatar_url')->label('رابط الصورة')->maxLength(500)->default(null),
                Forms\Components\TextInput::make('website')->label('الموقع الشخصي')->maxLength(500)->default(null),
                Forms\Components\Textarea::make('social_json')->label('روابط التواصل الاجتماعي')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('المعرف (slug)')->searchable(),
                Tables\Columns\TextColumn::make('role')->label('الدور'),
                Tables\Columns\TextColumn::make('avatar_url')->label('رابط الصورة')->searchable(),
                Tables\Columns\TextColumn::make('website')->label('الموقع الشخصي')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('تاريخ التحديث')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
