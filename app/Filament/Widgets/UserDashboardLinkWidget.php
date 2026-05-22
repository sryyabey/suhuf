<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class UserDashboardLinkWidget extends Widget
{
    protected string $view = 'filament.widgets.user-dashboard-link-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super-admin');
    }
}
