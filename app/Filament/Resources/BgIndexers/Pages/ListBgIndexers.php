<?php

namespace App\Filament\Resources\BgIndexers\Pages;

use App\Filament\Resources\BgIndexers\BgIndexerResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\BgIndexerBanner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ListBgIndexers extends ListRecords
{
  protected static string $resource = BgIndexerResource::class;

  protected $listeners = ['open-view-urls-modal' => 'openViewUrlsModal'];

  protected function getHeaderWidgets(): array
  {
    return [
      BgIndexerBanner::class,
    ];
  }
  protected function getHeaderActions(): array
  {

    $user             = Auth::user();
    $bg_indexer_points = DB::table('bg_indexer_points')->where('uid', $user->id)->first();
    $points           = $bg_indexer_points ? $bg_indexer_points->points : 0;

    return [

      CreateAction::make()->visible(fn() => $points > 0)->label('Create your Campaign')->icon('heroicon-m-plus'),

      Actions\Action::make('subscribeToCreate')
        ->label('Create your Campaign')
        ->icon('heroicon-m-plus')
        ->visible(fn() => $points <= 0)
        ->modalHeading('Your Remaining Points For Background Indexer = 0')
        ->modalDescription('To Access More Points click UPGRADE')
        ->modalSubmitActionLabel('UPGRADE')
        ->action(function () {
          return redirect()->route('pricing');
        })
        ->modalCancelActionLabel('Close'),
    ];

    // return [
    //   CreateAction::make(),
    // ];
  }

  public function openViewUrlsModal($data)
  {
    $record = (object) $data['record'];
    $path = 'background-indexer/' . $record->filename;
    $text = Storage::exists($path) ? Storage::get($path) : '';
    $lines = preg_split('/\r\n|\r|\n/', trim($text));
    $links = array_values(array_filter(array_map('trim', $lines)));
    $links = array_values(array_filter($links, fn($u) => filter_var($u, FILTER_VALIDATE_URL)));
    $this->modal()
      ->heading('URLs in ' . $record->filename)
      ->content(view('filament.bg-indexers.modals.view-urls', compact('links')))
      ->open();
  }
}
