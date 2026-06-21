<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BacklinksBanner extends Widget
{

  protected static ?int $sort = -1;
  protected static ?string $heading = null;
  protected static ?string $pollingInterval = '15s';

  protected int|string|array $columnSpan = 'full';

  public function render(): \Illuminate\View\View
  {
    $uid              = Auth::id();
    $backlinksPointsData  = DB::table('backlinks_points')->where('uid', $uid)->first();
    $backlinksPoints  = $backlinksPointsData ? $backlinksPointsData->points : 0;
    $backlinksUsed  = $backlinksPointsData ? $backlinksPointsData->used : 0;
    $availablePoints = $backlinksPoints - $backlinksUsed;
    return view('filament.widgets.backlinks-banner', [
      'points' => $backlinksPoints,
      'used' => $backlinksUsed,
      'available' => $availablePoints,
    ]);
  }

  public static function canView(): bool
  {
    return ! request()->routeIs('filament.dashboard.pages.dashboard');
  }
}


// class BacklinksBanner extends Widget
// {
//   protected static string $view = 'filament.widgets.backlinks-banner';
//   protected int|string|array $columnSpan = 'full';
//   protected static ?string $heading = null;
//   protected static ?string $pollingInterval = '15s'; // اختياري: ريفريش

//   protected function getViewData(): array
//   {

//     $uid = Auth::id();
//     $backlinksPoints = DB::table('backlinks')->where('uid', $uid)->first();
//     return compact('backlinksPoints');
//   }
// }
