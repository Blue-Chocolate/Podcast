<?php

namespace App\Filament\Widgets;

use App\Models\Release;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RealsesCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $count = Release::count();

        return [
            Stat::make('عدد الريلز', $count)
                ->description('إجمالي عدد الريلز المنشورة')
                ->color('warning')
                ->icon('heroicon-o-video-camera'),
        ];
    }
}
