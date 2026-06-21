<?php

namespace App\Filament\Resources\Backlinks\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Support\Enums\IconPosition;

class BacklinksTable
{
  public static function configure(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->label('#ID')
          ->formatStateUsing(fn($state) => '#' . $state),
        TextColumn::make('project_name')
          ->label('Project Name')
          ->limit(20),
        TextColumn::make('qty')
          ->label('Quantity'),
        TextColumn::make('links')
          ->label('Links')
          ->state(fn($record) => 'View (' . $record->urls_count . ')')
          ->icon(fn($record) => $record->urls_count > 0 ? 'heroicon-o-link' : null)
          ->extraAttributes(['class' => 'cursor-pointer'])
          ->action(
            Action::make('ViewUrls')
              ->modalHeading(fn($record) => 'URLs in Order ID: #' . $record->id)
              ->modalSubmitAction(false)
              ->modalCancelActionLabel('Close')
              ->modalContent(function ($record) {
                $text = is_array($record->links) ? implode("\n", $record->links) : $record->links;
                $lines = preg_split('/\r\n|\r|\n|,/', trim($text));
                $links = array_values(array_filter(array_map('trim', $lines)));
                $links = array_values(array_filter($links, fn($u) => filter_var($u, FILTER_VALIDATE_URL)));
                return view('filament.backlinks.modals.view-urls', compact('links'));
              })
          ),
        TextColumn::make('keywords')
          ->label('Keywords')
          ->state(fn($record) => 'View (' . $record->urls_count . ')')
          ->icon(fn($record) => $record->urls_count > 0 ? 'heroicon-o-link' : null)
          ->extraAttributes(['class' => 'cursor-pointer'])
          ->action(
            Action::make('ViewKeywords')
              ->modalHeading(fn($record) => 'Keywords in Order ID: #' . $record->id)
              ->modalSubmitAction(false)
              ->modalCancelActionLabel('Close')
              ->modalContent(function ($record) {
                $text = is_array($record->keywords) ? implode("\n", $record->keywords) : $record->keywords;
                $keywords = preg_split('/\r\n|\r|\n|,/', trim($text));
                $keywords = array_values(array_filter(array_map('trim', $keywords)));
                return view('filament.backlinks.modals.view-kw', compact('keywords'));
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
        TextColumn::make('report')
          ->label('Report')
          ->formatStateUsing(fn($state) => filled($state) ? 'Download Report' : '—')
          ->icon('heroicon-o-arrow-down-tray')
          ->iconPosition(IconPosition::Before)
          ->url(fn($record) => filled($record->report) ? $record->report : null, shouldOpenInNewTab: true),
        TextColumn::make('created_at')
          ->label('Date')
          ->since()
          ->tooltip(fn($record) => $record->updated_at?->format('Y-m-d H:i:s')),
      ])
      ->defaultSort('id', 'desc');
    // ->filters([
    //   //
    // ])
    // ->recordActions([
    //   ViewAction::make()
    //     ->icon('heroicon-o-eye')
    //     ->iconButton()
    //     ->modalHeading('Details')
    //     ->modalContent(function ($record) {
    //       $links = is_array($record->links) ? implode(', ', $record->links) : $record->links;
    //       $keyword = $record->keyword;
    //       return "<strong>Links:</strong> $links<br><strong>Keyword:</strong> $keyword";
    //     }),
    // ])
    // ->toolbarActions([
    //   // BulkActionGroup::make([
    //   //   DeleteBulkAction::make(),
    //   // ]),
    // ]);
  }
}
