<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets;
use App\Filament\Widgets\BlogsVisitsWidget;
use App\Filament\Widgets\PodcastsCountWidget;
use App\Filament\Widgets\RealsesCountWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'لوحة التحكم';
    protected static ?string $title = 'لوحة التحكم';
    protected static string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            BlogsVisitsWidget::class,
            PodcastsCountWidget::class,
            RealsesCountWidget::class,
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
