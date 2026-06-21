<?php

namespace App\Filament\Resources\Apps\Pages;

use App\Filament\Resources\Apps\AppResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class ListApps extends ListRecords
{
  protected static string $resource = AppResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Action::make('buy-app')
        ->label('Create new order licnesens')
        ->url(url('/buy-app'))
        ->icon('heroicon-o-plus')
        ->color('primary')
        ->button(),
    ];
  }

  // public function getHeader(): ?View
  // {
  //   return view('filament.widgets.app-license-hint');
  // }

  // protected function getHeader(): ?string
  // {
  //   return 'App License Key <br><small class="text-gray-500">You can view and manage your license keys here.</small>';
  // }
}
