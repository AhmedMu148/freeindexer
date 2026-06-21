<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BgIndexerBanner extends Widget
{
  protected static ?int $sort = -1;
  protected static ?string $heading = null;
  protected static ?string $pollingInterval = '15s';

  protected int|string|array $columnSpan = 'full';

  public function render(): \Illuminate\View\View
  {
    $uid                  = Auth::id();
    $bgIndexerPointsData  = DB::table('bg_indexer_points')->where('uid', $uid)->first();
    $bgIndexerPoints      = $bgIndexerPointsData ? $bgIndexerPointsData->points : 0;
    $bgIndexerUsed        = $bgIndexerPointsData ? $bgIndexerPointsData->used : 0;
    $availablePoints      = $bgIndexerPoints - $bgIndexerUsed;
    return view('filament.widgets.bg-indexer-banner', [
      'points' => $bgIndexerPoints,
      'used' => $bgIndexerUsed,
      'available' => $availablePoints,
    ]);
  }

  public static function canView(): bool
  {
    return ! request()->routeIs('filament.dashboard.pages.dashboard');
  }
}
