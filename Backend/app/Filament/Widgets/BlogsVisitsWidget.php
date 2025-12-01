<?php

namespace App\Filament\Widgets;

use App\Models\Blog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogsVisitsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalVisits = Blog::sum('views');

        return [
            Stat::make('إجمالي زيارات المدونات', $totalVisits)
                ->description('إجمالي عدد المشاهدات لجميع المدونات')
                ->color('success')
                ->icon('heroicon-o-eye'),
        ];
    }
}
