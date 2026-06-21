<?php

namespace App\Filament\Resources\Tickets\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class TicketsTable
{
  public static function configure(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->label('#ID')
          ->sortable()
          ->formatStateUsing(fn($state) => '#' . $state),
        TextColumn::make('subject')
          ->label('Subject'),
        TextColumn::make('status')
          ->label('Status')
          ->badge()
          ->colors(['success' => 'closed', 'warning' => 'pending', 'info' => 'open']),
        TextColumn::make('priority')
          ->label('Priority')
          ->badge()
          ->colors(['danger' => 'high', 'warning' => 'normal', 'success' => 'low']),
        TextColumn::make('updated_at')
          ->label('Last Updated')
          ->since()
          ->tooltip(fn($record) => $record->updated_at?->format('Y-m-d H:i:s')),
      ])
      ->defaultSort('id', 'desc')
      // ->filters([
      //   //
      // ])
      ->recordActions([
        // ViewAction::make(),
        EditAction::make()
          ->label('Check Ticket')
          ->icon('heroicon-o-eye'),
      ]);
    // ->emptyStateHeading('')
    // ->emptyStateDescription('')
    // ->toolbarActions([
    //   BulkActionGroup::make([
    //     DeleteBulkAction::make(),
    //   ]),
    // ]);
  }
}
