<?php

namespace App\Filament\Resources\SeasonResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EpisodesRelationManager extends RelationManager
{
    protected static string $relationship = 'episodes';
    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('عنوان الحلقة')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('episode_number')
                    ->label('رقم الحلقة')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('duration_seconds')
                    ->label('المدة (ثواني)')
                    ->numeric()
                    ->nullable(),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('title')->label('عنوان الحلقة')->searchable(),
                Tables\Columns\TextColumn::make('episode_number')->label('رقم الحلقة'),
                Tables\Columns\TextColumn::make('duration_seconds')->label('المدة (ثواني)'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('إضافة حلقة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}