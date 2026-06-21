<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppLicenseHint extends Widget
{

  protected static ?int $sort = -1;
  protected static ?string $heading = null;
  protected static ?string $pollingInterval = '15s';

  protected int|string|array $columnSpan = 'full';

  public function render(): \Illuminate\View\View
  {
    return view('filament.widgets.app-license-hint');
  }

  public static function canView(): bool
  {
    return ! request()->routeIs('filament.dashboard.pages.dashboard');
  }
}
