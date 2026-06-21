<?php

namespace App\Filament\Resources\BgIndexers\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Storage;

class BgIndexersTable
{
  public static function configure(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->label('#ID')
          ->formatStateUsing(fn($state) => '#' . $state),
        TextColumn::make('urls_count')
          ->label('Number Of URLs'),
        TextColumn::make('filename')
          ->label('View URLs')
          ->state(fn($record) => 'View (' . $record->urls_count . ')')
          ->icon(fn($record) => $record->urls_count > 0 ? 'heroicon-o-link' : null)
          ->extraAttributes(['class' => 'cursor-pointer'])
          ->action(
            Action::make('viewUrls')
              ->label('View URLs')
              ->icon('heroicon-o-link')
              ->modalHeading(fn($record) => 'URLs in ' . $record->filename)
              ->modalSubmitAction(false)
              ->modalCancelActionLabel('Close')
              ->modalContent(function ($record) {
                $path = 'background-indexer/' . $record->filename;
                $text = Storage::exists($path) ? Storage::get($path) : '';
                $lines = preg_split('/\r\n|\r|\n/', trim($text));
                $links = array_values(array_filter(array_map('trim', $lines)));
                $links = array_values(array_filter($links, fn($u) => filter_var($u, FILTER_VALIDATE_URL)));
                return view('filament.bg-indexers.modals.view-urls', compact('links'));
              })
          ),
        TextColumn::make('status_id')
          ->label('Status')
          ->badge()
          ->formatStateUsing(fn(string $state): string => match ($state) {
            '1'     => 'Pending',
            '2'     => 'Processing',
            '3'     => 'Completed',
            '4'     => 'Canceled',
            default => $state,
          })
          ->color(fn(string $state): string => match ($state) {
            '1'     => 'warning',
            '2'     => 'primary',
            '3'     => 'success',
            '4'     => 'danger',
            default => 'gray',
          }),
        TextColumn::make('created_at')
          ->label('Date')
          ->since()
          ->tooltip(fn($record) => $record->updated_at?->format('Y-m-d H:i:s')),
      ])
      ->defaultSort('id', 'desc');
    // ->actions([
    //   // DeleteAction::make()
    //   //   ->visible(
    //   //     fn($record) =>
    //   //     // in_array($record->status, ['pending', 'processing'])
    //   //   ),
    // ]);
  }
}
