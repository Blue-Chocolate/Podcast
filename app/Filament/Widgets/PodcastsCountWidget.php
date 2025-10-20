<?php

namespace App\Filament\Widgets;

use App\Models\Podcast;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PodcastsCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $count = Podcast::count();

        return [
            Stat::make('عدد البودكاستات', $count)
                ->description('إجمالي عدد ملفات البودكاست')
                ->color('primary')
                ->icon('heroicon-o-microphone'),
        ];
    }
}
