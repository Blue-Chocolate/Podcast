<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'إدارة';

    // Labels بالعربي
    public static function getModelLabel(): string
    {
        return 'مستخدم';
    }

    public static function getPluralModelLabel(): string
    {
        return 'المستخدمين';
    }

    // Form للإنشاء والتعديل
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label('كلمة المرور')
                    ->password()
                    ->required(fn($record) => !$record)
                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn($state) => filled($state)),

                Select::make('roles')
                    ->label('الأدوار')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload(),
            ]);
    }

    // Table لعرض المستخدمين
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('الرقم')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('البريد الإلكتروني')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('roles.name')->label('الأدوار')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->visible(fn($record) => $record->id !== auth()->id()) // تمنع حذف النفس
                    ->before(function ($record) {
                        if ($record->id === auth()->id()) {
                            $this->notify('danger', 'لا يمكنك حذف حسابك الشخصي.');
                            return false;
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('حذف جماعي')
                    ->visible(fn($records) => !collect($records)->contains(fn($r) => $r->id === auth()->id())), // تمنع حذف النفس جماعياً
            ]);
    }

    // Cache المستخدمين لتحسين الأداء
    public static function getCachedUsers()
    {
        return Cache::remember('users.all', now()->addMinutes(10), function () {
            return User::with('roles')->get();
        });
    }

    // مسح الكاش عند الإضافة أو التعديل أو الحذف
    public static function boot(): void
    {
        parent::boot();

        User::saved(fn() => Cache::forget('users.all'));
        User::deleted(fn() => Cache::forget('users.all'));
    }

    // صفحات Filament
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
