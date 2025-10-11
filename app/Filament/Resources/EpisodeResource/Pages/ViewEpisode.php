<?php

namespace App\Filament\Resources\EpisodeResource\Pages;

use App\Filament\Resources\EpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;

class ViewEpisode extends ViewRecord
{
    protected static string $resource = EpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Video Section
                Infolists\Components\Section::make('Video')
                    ->schema([
                        Infolists\Components\ViewEntry::make('video_url')
                            ->label('')
                            ->view('filament.infolists.video-player')
                            ->visible(fn ($record) => !empty($record->video_url)),
                        Infolists\Components\TextEntry::make('video_url')
                            ->label('Video URL')
                            ->url(fn ($record) => $record->video_url, shouldOpenInNewTab: true)
                            ->visible(fn ($record) => !empty($record->video_url)),
                    ])
                    ->visible(fn ($record) => !empty($record->video_url))
                    ->collapsible(),

                // Episode Information
                Infolists\Components\Section::make('Episode Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Title')
                                    ->weight(FontWeight::Bold)
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                                Infolists\Components\TextEntry::make('episode_number')
                                    ->label('Episode Number')
                                    ->badge()
                                    ->color('success'),
                            ]),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->markdown()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('short_description')
                            ->label('Short Description')
                            ->columnSpanFull(),
                    ]),

                // Media Details
                Infolists\Components\Section::make('Media Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\ImageEntry::make('cover_image')
                                    ->label('Cover Image')
                                    ->columnSpan(3),
                                Infolists\Components\TextEntry::make('duration_seconds')
                                    ->label('Duration')
                                    ->formatStateUsing(fn ($state) => gmdate('H:i:s', $state)),
                                Infolists\Components\TextEntry::make('file_size')
                                    ->label('File Size')
                                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1048576, 2) . ' MB' : 'N/A'),
                                Infolists\Components\TextEntry::make('mime_type')
                                    ->label('MIME Type'),
                                Infolists\Components\TextEntry::make('audio_url')
                                    ->label('Audio URL')
                                    ->url(fn ($record) => $record->audio_url, shouldOpenInNewTab: true)
                                    ->columnSpan(3)
                                    ->visible(fn ($record) => !empty($record->audio_url)),
                            ]),
                    ])
                    ->collapsible(),

                // Publishing Details
                Infolists\Components\Section::make('Publishing Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'published' => 'success',
                                        'draft' => 'warning',
                                        'scheduled' => 'info',
                                        default => 'gray',
                                    }),
                                Infolists\Components\IconEntry::make('explicit')
                                    ->label('Explicit Content')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-exclamation-triangle')
                                    ->falseIcon('heroicon-o-check-circle')
                                    ->trueColor('danger')
                                    ->falseColor('success'),
                                Infolists\Components\TextEntry::make('published_at')
                                    ->label('Published At')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('podcast.title')
                                    ->label('Podcast'),
                                Infolists\Components\TextEntry::make('season.title')
                                    ->label('Season')
                                    ->default('N/A'),
                                Infolists\Components\TextEntry::make('slug')
                                    ->label('Slug')
                                    ->copyable(),
                            ]),
                    ])
                    ->collapsible(),

                // Timestamps
                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}