<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Models\Organization;
use App\Models\Submission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Exporters\OrganizationExporter;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Organizations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Organization Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Organization Name'),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('sector')
                            ->maxLength(255)
                            ->placeholder('e.g., Technology, Finance, Healthcare'),

                        Forms\Components\DatePicker::make('established_at')
                            ->label('Established Date'),

                        Forms\Components\Textarea::make('address')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Submission Status')
                    ->schema([
                        Forms\Components\Select::make('submission_status')
                            ->options([
                                'pending' => 'Pending',
                                'submitted' => 'Submitted',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending'),
                    ]),

                Forms\Components\Section::make('Uploaded Files')
                    ->schema([
                        Forms\Components\FileUpload::make('strategy_plan_path')
                            ->label('Strategy Plan')
                            ->disk('public')
                            ->directory('uploads/organizations')
                            ->maxSize(20480),

                        Forms\Components\FileUpload::make('financial_report_path')
                            ->label('Financial Report')
                            ->disk('public')
                            ->directory('uploads/organizations')
                            ->maxSize(20480),

                        Forms\Components\FileUpload::make('structure_chart_path')
                            ->label('Structure Chart')
                            ->disk('public')
                            ->directory('uploads/organizations')
                            ->maxSize(20480),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('sector')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('phone')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('established_at')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('submission_status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'pending',
                        'info' => 'submitted',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check' => 'submitted',
                        'heroicon-o-check-circle' => 'approved',
                        'heroicon-o-x-circle' => 'rejected',
                    ])
                    ->sortable(),

                TextColumn::make('submission.total_score')
                    ->label('Total Score')
                    ->numeric( 2)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('submission_status')
                    ->options([
                        'pending' => 'Pending',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                SelectFilter::make('sector'),
            ])
            ->actions([
                EditAction::make(),

                Action::make('view_details')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Organization $record) => OrganizationResource::getUrl('view', ['record' => $record])),

                Action::make('view_submissions')
                    ->label('Submissions')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->modalHeading('Organization Submissions')
                    ->modalContent(function (Organization $record) {
                        $submissions = $record->submissions()->get();
                        return view('filament.modals.submissions', ['submissions' => $submissions]);
                    })
                    ->modalSubmitActionLabel('Close'),

                Action::make('change_status')
                    ->label('Change Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('New Status')
                            ->options([
                                'pending' => 'Pending',
                                'submitted' => 'Submitted',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                    ])
                    ->action(function (Organization $record, array $data) {
                        $record->update(['submission_status' => $data['status']]);
                    }),

                DeleteAction::make(),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('change_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('New Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'submitted' => 'Submitted',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update(['submission_status' => $data['status']]);
                            }
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Export All')
                    ->exporter(OrganizationExporter::class),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Organization Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Organization Name')
                            ->icon('heroicon-o-building-office'),

                        Infolists\Components\TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('phone')
                            ->label('Phone Number')
                            ->icon('heroicon-o-phone'),

                        Infolists\Components\TextEntry::make('sector')
                            ->label('Sector/Industry')
                            ->icon('heroicon-o-briefcase'),

                        Infolists\Components\TextEntry::make('established_at')
                            ->label('Established Date')
                            ->date('M d, Y')
                            ->icon('heroicon-o-calendar'),

                        Infolists\Components\TextEntry::make('address')
                            ->label('Address')
                            ->icon('heroicon-o-map-pin'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Submission Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('submission_status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'gray',
                                'submitted' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->icon(fn (string $state): string => match ($state) {
                                'pending' => 'heroicon-o-clock',
                                'submitted' => 'heroicon-o-check',
                                'approved' => 'heroicon-o-check-circle',
                                'rejected' => 'heroicon-o-x-circle',
                                default => 'heroicon-o-question-mark-circle',
                            }),

                        Infolists\Components\TextEntry::make('submission.total_score')
                            ->label('Total Score')
                            ->icon('heroicon-o-star'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Uploaded Documents')
                    ->schema([
                        Infolists\Components\TextEntry::make('strategy_plan_path')
                            ->label('Strategy Plan')
                            ->formatStateUsing(function ($state) {
                                if (!$state) {
                                    return 'No file uploaded';
                                }
                                $url = asset('storage/' . $state);
                                return $state;
                            })
                            ->url(fn ($record) => $record->strategy_plan_path ? asset('storage/' . $record->strategy_plan_path) : null)
                            ->openUrlInNewTab(),

                        Infolists\Components\TextEntry::make('financial_report_path')
                            ->label('Financial Report')
                            ->formatStateUsing(function ($state) {
                                if (!$state) {
                                    return 'No file uploaded';
                                }
                                return $state;
                            })
                            ->url(fn ($record) => $record->financial_report_path ? asset('storage/' . $record->financial_report_path) : null)
                            ->openUrlInNewTab(),

                        Infolists\Components\TextEntry::make('structure_chart_path')
                            ->label('Structure Chart')
                            ->formatStateUsing(function ($state) {
                                if (!$state) {
                                    return 'No file uploaded';
                                }
                                return $state;
                            })
                            ->url(fn ($record) => $record->structure_chart_path ? asset('storage/' . $record->structure_chart_path) : null)
                            ->openUrlInNewTab(),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-calendar'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-arrow-path'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view' => Pages\ViewOrganization::route('/{record}'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}