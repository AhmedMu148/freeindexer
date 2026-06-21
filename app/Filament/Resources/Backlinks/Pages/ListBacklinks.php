<?php

namespace App\Filament\Resources\Backlinks\Pages;

use App\Filament\Resources\Backlinks\BacklinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\BacklinksBanner;
use App\Filament\Resources\BgIndexers\BgIndexerResource;
use Filament\Actions;
use App\Filament\Widgets\BgIndexerBanner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ListBacklinks extends ListRecords
{
  protected static string $resource = BacklinkResource::class;

  protected $listeners = ['open-details-modal' => 'openDetailsModal'];

  // protected function getHeaderActions(): array
  // {
  //   return [
  //     CreateAction::make(),
  //   ];
  // }

  protected function getHeaderWidgets(): array
  {
    return [
      BacklinksBanner::class,
    ];
  }


  protected function getHeaderActions(): array
  {

    $user = Auth::user();
    $uid  = $user->id;
    $backlinks_points_data = DB::table('backlinks_points')->where('uid', $uid)->first();
    $points = $backlinks_points_data ? $backlinks_points_data->points : 0;

    return [

      CreateAction::make()->visible(fn() => $points > 0)->label('Create your Campaign')->icon('heroicon-m-plus'),

      Actions\Action::make('subscribeToCreate')
        ->label('Create your Campaign')
        ->icon('heroicon-m-plus')
        ->visible(fn() => $points <= 0)
        ->modalHeading('Your Remaining Points For SEO Backlinks = 0')
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

  public function openDetailsModal($data)
  {
    $record = (object) $data['record'];
    $links = is_array($record->links) ? implode(', ', $record->links) : $record->links;
    $keyword = $record->keyword;
    $this->modal()
      ->heading('Details')
      ->content("<strong>Links:</strong> $links<br><strong>Keyword:</strong> $keyword")
      ->open();
  }

  // public function openViewUrlsModal($data)
  // {
  //   $record = (object) $data['record'];
  //   $path = 'background-indexer/' . $record->filename;
  //   $text = Storage::exists($path) ? Storage::get($path) : '';
  //   $lines = preg_split('/\r\n|\r|\n/', trim($text));
  //   $links = array_values(array_filter(array_map('trim', $lines)));
  //   $links = array_values(array_filter($links, fn($u) => filter_var($u, FILTER_VALIDATE_URL)));
  //   $this->modal()
  //     ->heading('URLs in ' . $record->filename)
  //     ->content(view('filament.bg-indexers.modals.view-urls', compact('links')))
  //     ->open();
  // }
}
