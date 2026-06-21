<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardStats extends BaseWidget
{
  protected ?string $heading = 'Analytics';
  protected ?string $pollingInterval = '60s';
  // protected int|string|array $columnSpan = 'full';

  protected int|string|array $columnSpan = [
    'default' => 1,
    'md' => 3,
  ];

  protected function getStats(): array
  {

    $uid = Auth::id();

    $indexerPoints            = DB::table('indexer_points')->where('uid', $uid)->first();
    $indexerAvailablePoints   = $indexerPoints ? $indexerPoints->points - $indexerPoints->used : 0;

    $bgIndexerPoints          = DB::table('bg_indexer_points')->where('uid', $uid)->first();
    $bgIndexerAvailablePoints = $bgIndexerPoints ? $bgIndexerPoints->points - $bgIndexerPoints->used : 0;

    $backlinksPoints          = DB::table('backlinks_points')->where('uid', $uid)->first();
    $backlinksAvailablePoints = $backlinksPoints ? $backlinksPoints->points - $backlinksPoints->used : 0;

    return [
      Stat::make('Indexer', number_format($indexerAvailablePoints) . ' Points')
        ->descriptionIcon('heroicon-m-arrow-trending-up')
        ->icon('heroicon-o-users')
        ->color('success')
        ->columnSpan(1),

      Stat::make('Background indexer', number_format($bgIndexerAvailablePoints) . ' Points')
        ->descriptionIcon('heroicon-m-user-plus')
        ->icon('heroicon-o-user-group')
        ->color('success')
        ->columnSpan(1),

      Stat::make('SEO BackLinks', number_format($backlinksAvailablePoints) . ' Points')
        ->descriptionIcon('heroicon-m-check')
        ->icon('heroicon-o-check-badge')
        ->color('success')
        ->columnSpan(1),
    ];
  }
}
