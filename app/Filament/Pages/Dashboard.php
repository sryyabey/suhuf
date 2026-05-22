<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\UserDashboardLinkWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getHeaderWidgets(): array
    {
        return [
            UserDashboardLinkWidget::class,
        ];
    }
}
